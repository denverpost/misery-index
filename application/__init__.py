#!/usr/bin/env python
import os
import json
from flask import Flask
from flask import Markup
from flask_flatpages import FlatPages
from datetime import date

app = Flask(__name__)
app.debug = True

# This dict is called on every .html document.
# We initialize it here in case all the fields aren't defined by the view method.
page = {
    'title': '',
    'url': '',
    'description': ''
}

pages = FlatPages(app)

# SITE CONFIG
# Most of these vars are used on the site in some way.
# We store them here and then pass them to the template (you see them as response.app....)
with app.app_context():
    app.url_root = '/'
    app.page = page
    app.sitename = 'Rockies Misery Report'
    app.seasons = ['2016', '2015']
    app.season = app.seasons[0]

import application.flatpage
import application.thesite
