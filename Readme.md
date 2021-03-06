  INSTALLATION
================

Download jquery.timeago.js from http://timeago.yarp.com/jquery.timeago.js and
put it in the timeago module folder. Then the timeago plugin should exist at
libraries/timeago/jquery.timeago.js.


  OVERVIEW
============

This module uses the jQuery timeago plugin to create dynamically updating
"time ago" dates. That is, the plugin turns static dates like
"October 10, 2011" into "10 minutes ago" and updates the time ago every minute.
This allows you to include "time ago" dates in cached content for most users
while degrading gracefully for users with JavaScript disabled. For more
information and examples, visit the jQuery plugin's homepage at
http://timeago.yarp.com/


============
  FEATURES
============

 - The ability to use Timeago dates for virtually any date on the site
   including node created dates and comment created dates
 - Tokens for node created dates and comment created dates
 - An option to use the new HTML5 "time" element, abbr, or span
 - An API to turn any UNIX timestamp into a timeago date

Additionally the Statuses module integrates with Timeago
(https://drupal.org/project/statuses).


=======
  API
=======

The easiest way to construct a Timeago date is to use timeago_format_date():

/**
 * Converts a timestamp into a Timeago date.
 *
 * @param $timestamp
 *   A UNIX timestamp.
 * @param $date
 *   (Optional) A human-readable date (will be displayed if JS is disabled).
 *   If not provided, the site default date format is used.
 * @return
 *   HTML representing a Timeago-friendly date.
 */
function timeago_format_date($timestamp, $date = NULL)
If you want to manually construct a Timeago date, you can do so by creating
a timeago-compatible HTML element like below:
  <abbr class="timeago" title="2008-07-17T09:24:17Z">July 17, 2008</abbr>
  <span class="timeago" title="2008-07-17T09:24:17Z">July 17, 2008</span>
  <time class="timeago" datetime="2008-07-17T09:24:17Z">July 17, 2008</time>
The <time> tag is new in HTML5. The markup above will be turned into something
like this:
  <abbr class="timeago" title="July 17, 2008">3 years ago</abbr>

  TRANSLATION
===============

This module produces strings like "a moment ago" and "10 minutes ago" using
JavaScript, and these strings are passed through Drupal.t(), so they are
translate-able for most languages. However, some languages (Arabic, Polish,
Russian, Ukranian, etc.) have different suffixes depending on the numbers used.
If you need support for these languages, you can override the settings Timeago
uses in JavaScript by providing a translation override file. Examples of such
files are available at https://gist.github.com/6251 for various languages. You
should choose a translation or write your own and save it in a file named
jquery.timeago.LANGCODE.js, where LANGCODE is a language identifier code like
ar, pl, ru, or uk (for Arabic, Polish, Russian, and Ukranian, respectively).
This translation file should be placed in the module's folder, e.g. at
/sites/all/modules/timeago/jquery.timeago.ru.js for Russian. The appropriate
translation override file will be automatically added to the page if necessary.

