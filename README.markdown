# Field: Filter

- Version: 1.1
- Author: Marcin Konicki (http://ahwayakchih.neoni.net)
- Build Date: 25 April 2011
- Requirements: Symphony version 2.2 or later.


## Overview

Field: Filter allows to use expressions to conditionally filter a data source by URL parameters (`{$param}` syntax).

Symphony allows to filter with parameters, but without a way to filter conditionally, i.e., depending on parameter values.

After adding this field to a section, you will be able to filter data source using expressions like this:

	(if value of ({$entry}) is (welcome))

It will allow data source to load entries only when value of `$entry` parameter is equal to `welcome`.

Another example:

	(if any of ({$ds-list}) is in (one,two,three,{$param}))

That will load entries only if any of the values found in `$ds-list` parameter is equal to `one`, `two`, `three` or value of `$param` parameter.

Of course most of that is also possible with built-in filtering, so Filter field is useful mainly when filtering data source by data source generated parameters or with really complicated stuff (which probably could be simplified by changing site structure a bit :).

Filter field can also be used to minimize number of SQL queries, because every time a filter expression evaluates to `false`, database will not be queried at all and data source will output an empty set.


## Installation

1. Upload the 'filterfield' folder in this archive to your Symphony 'extensions' folder.
2. Enable it by selecting the "Field: Filter", choose Enable from the with-selected menu, then click Apply.


## Changelog

- **1.1** Allow using an expression to prevent an entry from being saved. Value filtering expressions can now use {XPath}. Filter field will now store `yes` or `no` (when field expression evaluates to `false`) value in database.
- **1.0** Initial release.


## Usage

To filter data source:

1. Add a Filter field to a section which you want to be filtered by data source with a filter expression.
2. Check "Allow data sources to filter this section with an expression" field.
3. Select filter field as one of the filters on data source edit page.
4. Enter an expression which, when evaluated to `false`, will block data source from querying database.
5. You can also enter value by which data source should be filtered. Just like when filtering data source with checkbox field.
6. Enter both an expression and a value to make Filter field evaluate that expression first and, if it returns `true`, filter entries by that value.

To allow or disallow saving of an entry:

1. Add a Filter field to a section of which entry values should be filtered by an expression.
2. Enter a filter expression which will be evaluated every time an entry is being saved. If evaluation will return `false`, value of a field will be set to `no`. Otherwise it will be set to `yes`. You can use `{XPath}` syntax for an expression to make use of values found in XML that contains `post`, `author`, `old-entry` (only when entry is edited) and `entry` elements, e.g., to check value of a field called `fields[published]` enter `{post/published}`.
3. Check "Allow saving an entry only when expression entered above evaluates to true" if a field should prevent an entry from being saved to database after value filter expression evaluates to `false`.
4. Edit an entry and see if it can or cannot be saved to database :).


## Syntax

	(if SELECTOR (VALUES) OPERAND (VALUES)) 

Expression has to start and end with parenthesis.
There has to be "if" at the start of expression (just in case there will be other functions added in future).

SELECTOR can be "value of", "any of" or "all of".

VALUES is comma separated list of values. Each of them can be either literal value, {$param} or sub expression. Every VALUES has to be inside parenthesis. It can be empty, but parenthesis is required.

OPERAND can be "is", "is not", "is in" or "is not in".


## Examples of filtering field's value when publishing an entry

If section has a checkbox field called "published" and an entry should not become "unpublished" after it was saved at least once before:

	(if any of ((if value of ({concat('id', old-entry/@id)}) is (id)), (if value of (yes) is ({post/published}))) is (yes))

It concats string to `old-entry/@id`, just in case old-entry is not there (and returns empty string). So, if old-entry is there, value will become `id123` (number will be set to entry ID). If old-entry is not there, value will become `id`.
Expression will evaluate to `true` if it is a new entry (old-entry is not there, so `id` is equal to `id`) or if POSTed value of `fields[published]` is `yes`.

If, at the same time, filter field is configured to "Allow saving an entry only when expression entered above evaluates to true", entry will not be saved if expression evaluates to `false`.

The same result can also be achieved with a bit simpler expression:

	(if any of ((if value of ({old-entry/@id}) is ()), (if value of (yes) is ({post/published}))) is (yes))


## Examples of filtering a data source

Allow data source to execute only when value of data source generated `$ds-type` parameter is `mytype`, or value of data source generated `$ds-true-or-false` parameter is `yes`:

	(if any of ((if value of ({$ds-type}) is (mytype)), {$ds-true-or-false}) is (yes))

Allow data source to execute only when any value of data source generated parameter can be found in value of another data source generated param:

	(if any of ({$ds-list}) is in ({$ds-different-list}))

Allow data source to execute only when value of author parameter (passed through URL param) can be found in value of parameter generated by data source:

	(if value of ({$author}) is in ({$ds-list-of-authors-with-additional-info}))

Allow data source to execute only when value of parameter passed thorugh URL path is `symphony`:

	(if value of ({$name}) is (symphony))

Allow data source to execute only when any of values found in data source generated parameter is empty/null:

    (if any of ({$ds-names}) is ())

Allow data source to execute only when any of values found in data source generated parameter is not `empty`/`null`, and then select entries only when they passed publish filter expression (or if there was no publish filtering when they were being saved to database):

    (if any of ({$ds-names}) is not ()), yes

