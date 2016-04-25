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
    app.page['title'] = ''
    app.page['description'] = ''
    app.page['url'] = build_url(app, request)

    response = {
        'app': app
    }
    return render_template('home.html', response=response)

# =========================================================
# === NOT DEPLOYED YET === #
# =========================================================

@app.route('/season/')
def season_index():
    app.page['title'] = ''
    app.page['description'] = ''
    app.page['url'] = build_url(app, request)

    response = {
        'app': app
    }
    return render_template('season_index.html', response=response)

@app.route('/season/<year>/')
def season_detail(year):
    app.page['title'] = ''
    app.page['description'] = ''
    app.page['url'] = build_url(app, request)

    items = json.load(open('output/%s.json' % year))

    # We want the distinct months
    months = []
    for item in items:
        dt = datetime.strptime(item['Date'], '%m/%d/%Y')
        if dt.month not in months:
            months.append(dt.month)

    response = {
        'app': app,
        'months': months
    }
    return render_template('season_detail.html', response=response)

@app.route('/season/<year>/<month>/')
def month_detail(year, month):
    app.page['title'] = ''
    app.page['description'] = ''
    app.page['url'] = build_url(app, request)

    items = json.load(open('output/%s.json' % year))

    # We want the distinct events 
    events = []
    for item in items:
        dt = datetime.strptime(item['Date'], '%m/%d/%Y')
        if dt.month != month:
            events.append(item)

    response = {
        'app': app,
        'events': events
    }
    return render_template('month_detail.html', response=response)

