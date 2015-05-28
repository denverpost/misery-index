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
import datetime, time
import gspread
from spreadsheet import Sheet
from collections import defaultdict
try:
    from collections import OrderedDict
except ImportError:
    # python 2.6 or earlier, use backport
    from ordereddict import OrderedDict
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

        rows = self.sheet.sheet.get_all_values()
        keys = rows[0]
        fn = {
            'json': open('%s/output/%s.json' % (self.sheet.directory, self.sheet.filename), 'wb'),
            'jsonp': open('%s/output/%s.jsonp' % (self.sheet.directory, self.sheet.filename), 'wb'),
            'csv': open('%s/output/%s.csv' % (self.sheet.directory, self.sheet.filename), 'wb')
        }
        recordwriter = UnicodeWriter(fn['csv'], delimiter=',', 
                                     quotechar='"', quoting=csv.QUOTE_MINIMAL)
        records = []
        for i, row in enumerate(rows):
            if i == 0:
                keys = row
                recordwriter.writerow(keys)
                continue
            record = OrderedDict(zip(keys, row))

            # We write lines one-by-one. If we have filters, we run
            # through them here to see if we're handling a record we
            # shouldn't be writing.
            publish = True
            if self.sheet.filters:
                for item in self.sheet.filters:
                    # Special handling for filtering by years. Hard-coded.
                    if item['key'] == 'Year':
                        if item['value'] not in record['Date of misery']:
                            publish = False
                    elif record[item['key']] != item['value']:
                        publish = False

            if publish:
                # Turn the date into a timestamp.
                try:
                    timestamp = record['Timestamp']
                    if record['Datetime'] != '':
                        timestamp = record['Datetime']
                    record['unixtime'] = int(time.mktime(
                                                         datetime.datetime.strptime(timestamp,
                                                         "%m/%d/%Y %X").timetuple()))
                except:
                    record['unixtime'] = 0
                
                recordwriter.writerow(row)
                records += [record]

        if records:
            json.dump(records, fn['json'])
            content = json.dumps(records)
            fn['jsonp'].write('misery_callback(%s);' % content)

        return True

def main(args):
    """ 
        """
    sheet = Sheet('Misery Index', 'responses')
    sheet.set_options(args)
    misery = Misery(sheet)
    misery.publish()

if __name__ == '__main__':
    parser = argparse.ArgumentParser(usage='$ python misery.py',
                                     description='Downloads, filters and re-publishes the Google sheet of Bad Things.',
                                     epilog='')
    parser.add_argument("-g", "--geocode", dest="geocode", default=False, action="store_true")
    parser.add_argument("-v", "--verbose", dest="verbose", default=False, action="store_true")
    args = parser.parse_args()

    if args.verbose:
        doctest.testmod(verbose=args.verbose)

    main(args)
