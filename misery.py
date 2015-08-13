#!/usr/bin/env python
"""misery.py docstring
Turn the misery spreadsheet into a flatfile.
"""
import os
import sys
import json
import doctest
import csv
import codecs, cStringIO
from datetime import date, datetime, timedelta
import time
import gspread
import math
from spreadsheet import Sheet
from collections import defaultdict, OrderedDict
import argparse


class UnicodeWriter:
    """
    A CSV writer which will write rows to CSV file "f",
    which is encoded in the given encoding.
    """

    def __init__(self, f, dialect=csv.excel, encoding="utf-8", **kwds):
        # Redirect output to a queue
        self.queue = cStringIO.StringIO()
        self.writer = csv.writer(self.queue, dialect=dialect, **kwds)
        self.stream = f
        self.encoder = codecs.getincrementalencoder(encoding)()

    def writerow(self, row):
        self.writer.writerow([s.encode("utf-8") for s in row])
        # Fetch UTF-8 output from the queue ...
        data = self.queue.getvalue()
        data = data.decode("utf-8")
        # ... and reencode it into the target encoding
        data = self.encoder.encode(data)
        # write to the target stream
        self.stream.write(data)
        # empty queue
        self.queue.truncate(0)

    def writerows(self, rows):
        for row in rows:
            self.writerow(row)


class Misery:
    """ Handle the misery spreadsheet-specific parts of publishing
        from Google Sheets.
        """

    def __init__(self, sheet):
        self.sheet = sheet
        self.is_metro = False

    def publish(self, worksheet=None):
        """ Publish the misery data in whatever permutations we need.
            This assumes the spreadsheet's key names are in the first row.
            >>> sheet = Sheet('test-sheet', 'worksheet-name')
            >>> misery = Misery(sheet)
            >>> misery.publish()
            True
            """
        if not self.sheet.sheet or worksheet:
            self.sheet.sheet = self.open_worksheet(worksheet)

        if not worksheet:
            worksheet = self.sheet.worksheet

        self.sheet.build_filename()

        self.fn = {
            'json': open('%s/output/%s.json' % (self.sheet.directory, self.sheet.filename), 'wb'),
            'jsonp': open('%s/output/%s.jsonp' % (self.sheet.directory, self.sheet.filename), 'wb'),
            'csv': open('%s/output/%s.csv' % (self.sheet.directory, self.sheet.filename), 'wb')
        }
        rows = self.sheet.sheet.get_all_values()
        records = self.get_records(rows)

        # Now build the day-by-day Misery Indexes.
        # We'll have a list of date/value pairs by the end of this.
        items = []
        for record in records:
            items.append((record['Date'], record['Value']))
        self.items = items
        scores = self.calc_score()
        if scores:
            fn = 'scores'
            if 'live' in self.sheet.filename:
                fn += '-live'

            fh = open('output/%s.json' % fn, 'wb')
            json.dump(scores, fh)
            fh.close()
            content = json.dumps(scores)
            fh = open('output/%s.jsonp' % fn, 'wb')
            fh.write('misery_scores_callback(%s);' % content)
            fh.close()
            

        if records:
            json.dump(records, self.fn['json'])
            content = json.dumps(records)
            self.fn['jsonp'].write('misery_callback(%s);' % content)

        return True

    def get_records(self, rows):
        """ Put together a record set from the main Misery sheet.
            # {'Bad Thing': 'Test two', 'Timestamp': '5/27/2015 17:01:39', 'URL': '', 'Value': '7', 'Date': '5/26/2015'}
            """
        recordwriter = UnicodeWriter(self.fn['csv'], delimiter=',', 
                                     quotechar='"', quoting=csv.QUOTE_MINIMAL)
        keys = rows[0]
        recordwriter.writerow(keys)
        records = []
        for i, row in enumerate(rows):
            if i == 0:
                continue
            record = dict(zip(keys, row))

            day = None
            if 'Timestamp' in record:
                record, day = self.handle_timestamp(record)

            # We want to know how many days ago this happened,
            # and add that to the record.
            if day:
                days_ago = datetime.today() - day
                record['ago'] = days_ago.days
            
            recordwriter.writerow(row)
            records += [record]

        return records

    def handle_timestamp(self, record):
        """ We may need this code more than once, so:
            """
        # Turn the date into a timestamp.
        day = None
        try:
            timestamp = record['Timestamp'].split(' ')[0]
            record['Timestamp'] = timestamp
            if record['Date'] == '':
                # We do this so we can use the Date field from here on out.
                record['Date'] = record['Timestamp']
            else:
                timestamp = record['Date']
            day = datetime.strptime(timestamp, "%m/%d/%Y")
            record['unixtime'] = int(time.mktime(day.timetuple()))
        except:
            record['unixtime'] = 0
        return record, day

    def publish_scores(self):
        """
            """
        pass

    def calc_score(self):
        """ Given a dict of date/score tuples, return a per-day list of date/score tuples.
            We use an OrderedDict so we know what the first day of events is --
            we can't be certain that something happens on every day, so we use
            the first-day to populate a dict with every date between then and now.

            Keep in mind more than one event can happen per day.
            Also keep in mind that an event on one day still affects the 
            next day's score -- it's worth half what it was worth the day before.
            """
        # items is expected to look something like
        # [('6/1/2015', '2'), ('6/2/2015', '10'), ('6/3/2015', '4')]

        # Consolidate the dates so we can make a set with this object.
        event_items = OrderedDict()
        distinct_items = OrderedDict()
        for item in self.items:
            if item[0] not in event_items:
                event_items[item[0]] = 0
        first_item = datetime.strptime(next(iter(event_items)), "%m/%d/%Y").date()
        today = date.today()

        # Fill out any empty dates with zero-values
        i = 0
        while True:
            item = first_item + timedelta(days=i)
            item_str = date.strftime(item, "%-m/%-d/%Y")
            distinct_items[item_str] = 0
            i += 1
            if item == today:
                break

        # Add up the raw score.
        for item in self.items:
            distinct_items[item[0]] += int(item[1])

        # Now loop through the raw score, and calculate the total scores.
        # The next day's score is equal to half of the previous day's score plus any new events.
        previous_score = 0
        for item in iter(distinct_items):
            score = round(previous_score/float(2.1)) + distinct_items[item]
            distinct_items[item] = score
            previous_score = score

        return distinct_items


class MiseryLive(Misery):

    def calc_score(self):
        """ Given a dict of date/score tuples, return a per-day list of date/score tuples.
            We use an OrderedDict so we know what the first day of events is --
            we can't be certain that something happens on every day, so we use
            the first-day to populate a dict with every date between then and now.

            Keep in mind more than one event can happen per day.
            Also keep in mind that an event on one day still affects the 
            next day's score -- it's worth half what it was worth the day before.
            """
        # items is expected to look something like
        # [('1', '2'), ('1', '10'), ('4', '4')]

        event_items = OrderedDict()
        distinct_items = OrderedDict({'1': 0, '2': 0, '3': 0, '4': 0, '5': 0, '6': 0, '7': 0, '8': 0, '9': 0})
        for item in self.items:
            if item[0] not in event_items:
                event_items[item[0]] = 0
        first_item = 1
        endpoint = 9

        # Add up the raw score.
        for item in self.items:
            distinct_items[item[0]] += int(item[1])

        return distinct_items

def main(args):
    """ 
        """
    sheet = Sheet('Misery Index', args.name)
    sheet.set_options(args)

    if args.live:
        misery = MiseryLive(sheet)
    else:
        misery = Misery(sheet)

    misery.publish()

def build_parser(args):
    """ A method to handle argparse.
        """
    parser = argparse.ArgumentParser(usage='$ python misery.py',
                                     description='''Downloads, filters and 
                                                    re-publishes the Google
                                                    sheet of Bad Things that
                                                    the Rockies have done /
                                                    had done to the team.''',
                                     epilog='')
    parser.add_argument("-v", "--verbose", dest="verbose", default=False, action="store_true")
    parser.add_argument("-n", "--name", dest="name", default='responses')
    parser.add_argument("-l", "--live", dest="live", default=False, action="store_true")
    return parser.parse_args()

if __name__ == '__main__':
    args = build_parser(sys.argv)

    if args.verbose:
        doctest.testmod(verbose=args.verbose)

    main(args)
