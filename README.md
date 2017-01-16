# Misery Index
Let's quantify how miserable the Colorado Rockies are, daily.

![Sad Dinger](http://extras.mnginteractive.com/live/media/site36/2015/0624/20150624_043103_sad_dinger.gif)

## How-to's

### How to set up a dev environment to work on the Misery Index

1. Clone this repo to your computer.
1. Create a virtual environment for this project. (If this item doesn't make sense to you, [https://github.com/denverpost/stat-tracker#how-to-set-up-your-dev-environment](read these instructions here))
1. Activate the virtual env if you haven't yet.
1. Install the requirements with `pip install -r requirements.txt`
1. Add two environment variables, `ACCOUNT_USER` and `ACCOUNT_KEY`. You can get the values for these variables by setting up an Oauth2 key for the Misery Index spreadsheet with Google, and [this page will tell you how to set up Oauth2 access to that spreadsheet](http://gspread.readthedocs.io/en/latest/oauth2.html).


### How to set the Misery Index up for a new year

1. Un-comment-out the misery index cron job on prod.

### How the Misery Index works.

# License

The MIT License (MIT)

Copyright © 2015-2017 The Denver Post

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
