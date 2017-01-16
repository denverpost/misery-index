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
1. Create a new tab and name it after the current year. If you want to hook a Google Form up to the sheet, create the Form ([it should look similar to this](https://docs.google.com/forms/d/e/1FAIpQLScLoPIC6GWdCcP7ib7TRhNkT34zocPZAO7EBe3xz4YxzPwZQQ/viewform)) and after you finish creating it, Google will give you an option to tie it to an existing sheet. Do that.
1. Edit the "YEAR" variable in [deploy.bash](deploy.bash) so it's the current year. 
1. Copy [www/index.html](www/index.html), name it after the previous year. So, in the 2017 season, you would rename www/index.html www/2016.html. Push that new file to production.
1. Edit [the previous year's Misery Index article](http://www.denverpost.com/2016/04/25/colorado-rockies-misery-index-2016/) to point to the new previous year's file.
1. Also change the filename of http://extras.denverpost.com/app/misery-index/output/scores.json to http://extras.denverpost.com/app/misery-index/output/scores-PREVIOUSYEAR.json (where "PREVIOUSYEAR" would be 2016 or 2017 or whatever the previous year was), and update the filename reference in the previous year's markup to point to it.
1. Edit www/index.html so it reflects the current season.
1. Sorry this is so janky.

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
