#!/usr/bin/env python
from flask import g, render_template, url_for, redirect, abort, request
from datetime import datetime, date, timedelta
from collections import OrderedDict
import inspect
import os
import json
import string
from application import app
import filters
from werkzeug.contrib.atom import AtomFeed


datetimeformat = '%Y-%m-%d %H:%M:%S'

def build_url(app, request):
    """ Return a URL for the current view.
        """
    return '%s%s' % (app.url_root, request.path[1:])

# =========================================================
# HOMEPAGE VIEW
# =========================================================

@app.route('/')
def index():
    app.page['title'] = 'Misery Index Archive'
    app.page['description'] = ''
    app.page['url'] = build_url(app, request)

    response = {
        'app': app
    }
    return render_template('home.html', response=response)

# =========================================================
# === NOT DEPLOYED YET === #
# =========================================================
def sport_lookup(sport):
    """ Take the URL parameter and make it a publishable string.
        """
    return sport.title()

@app.route('/<sport>/')
def sport_index(sport):
    app.page['title'] = '%s Misery Index Archive' % sport_lookup(sport)
    app.page['description'] = ''
    app.page['url'] = build_url(app, request)

    response = {
        'app': app
    }
    return render_template('sport_index.html', response=response)

@app.route('/<sport>/season/')
def season_index(sport):
    app.page['title'] = '%s Misery Index Index' % sport_lookup(sport)
    app.page['description'] = ''
    app.page['url'] = build_url(app, request)

    response = {
        'app': app
    }
    return render_template('season_index.html', response=response)

@app.route('/<sport>/season/<year>/')
def season_detail(sport, year):
    app.page['title'] = '%s Misery Index %s'  % (sport_lookup(sport), year)
    app.page['description'] = ''
    app.page['url'] = build_url(app, request)

    items = json.load(open('output/%s.json' % year))

    # We want the distinct months
    months = []
    for item in items:
        dt = datetime.strptime(item['Date'], '%m/%d/%Y')
        if filters.month_l_filter(dt.month) not in months:
            months.append(filters.month_l_filter(dt.month))

    response = {
        'app': app,
        'count': len(items),
        'year': year,
        'months': months
    }
    return render_template('season_detail.html', response=response)

def month_overview(items, month_long):
    """ This returns all the data we use in a month report.
        It also allows us to compare one month's events to another.
        It returns a dict.
        """
    events = []
    for item in items:
        dt = datetime.strptime(item['Date'], '%m/%d/%Y')
        if filters.month_l_filter(dt.month) == month_long:
            events.append(item)
    return dict(events=events)

@app.route('/<sport>/season/<year>/<month_long>/')
def month_detail(sport, year, month_long):
    app.page['title'] = '%s Misery Report, %s %s' % (sport_lookup(sport), month_long.title(), year)
    app.page['description'] = 'The %s Misery Index Report for %s %s' % (sport_lookup(sport), month_long.title(), year)
    app.page['url'] = build_url(app, request)

    items = json.load(open('output/%s.json' % year))

    month = month_overview(items, month_long)

    response = {
        'app': app,
        'year': year,
        'month_long': month_long,
        'count': len(month['events']),
        'month': month
    }
    return render_template('month_detail.html', response=response)

