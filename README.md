
A basic REST API with Restler and Backbone
==========================================

TL;DR:
------
The following describes how to create a basic REST API with [Restler](http://luracast.com/products/restler/) and [Backbone](http://documentcloud.github.com/backbone/#).   

Overview:
---------
A recent job involved making some data accessible online. I used the following tools to convert raw data (property information) into a [**REST**](http://en.wikipedia.org/wiki/Representational_state_transfer)ful API so that it could be easily accessed from any client. In this case an HTML front-end was chosen, but the data may just as well have been pulled from an Android or iOS device.

Steps taken:  

* A python script was built to convert a .csv file of raw data into a MySQL script. This was used to populate the `properties` table.  
* PHP classes were built to represent and validate property objects, as well as save and retrieve records from the `properties` table.  
* [Restler 3.0](http://luracast.com/products/restler/) was used to create an HTTP web service API from the PHP classes.  
* [Backbone.js](http://documentcloud.github.com/backbone/#) was used to connect a web page to the API over a RESTful JSON interface.   

Online demo:
------------
Don't get too excited, it's only a CRUD app ;) but the front-end is totally driven by AJAX and Backbone - no form posts are involved:   
[A basic REST API with Restler and Backbone](http://bit.ly/UB486e)   

REST URL:   
[http://bit-taming.com/content/projects/restler-backbone-example/api/property](http://bit-taming.com/content/projects/restler-backbone-example/api/property)    

Prerequisites:
--------------
* The development and test environment was Mac OSX (10.6.8), **A**pache, **M**ySQL (5.1.65) and **P**HP (5.3.15). See **useful resources** below for pointers on setting up an **AMP** development environment on Mac OSX.  
* Python was used to convert the sample data to a MySQL script. See **useful resources** below for pointers on setting up python on Mac OSX.  

Install instructions:
---------------------
* This web application was built to run on the Apache/MySQL/PHP stack. See **useful resources** below for pointers on setting up an **AMP** development environment on Mac OSX.  
(UPDATE!!! I've just successfully run the app on a [WAMPServer](http://www.wampserver.com/en/#) on Windows 7, with no reconfiguration necessary)   
* Download and copy the files to some sub-folder of your web server.   
* The SQL script to create the MySQL database can be found in `resources/db_script.mysql`. In this script you will need to adjust the database name and user names and passwords appropriately. I used **PHPMyAdmin** (see resources below for setup details) to create the database, users and tables.  
* I wrote a python script (`resources/build_table_script.py`) to generate the MySQL script necessary to create the database table and records (`resources/table_script.mysql`). The raw sample data can be found in `resources/sample.csv`. See **useful resources** below for pointers on setting up a python environment on Mac OSX.
* `pdo/conn.php` contains the database connection settings. you will need to adjust these settings appropriately.  
* In `lib/core.php`, `set_include_path` needs to be properly configured. I set this to the path of the app's home folder.  
* The front-end needs to be configured to pull data from the correct URL. Open `js/app.js`, search for "NOTE!!!", and update the URLs accordingly.
* Browse to `./index.html`. Hopefully you will see a table displaying the retrieved property records.
* That's it! If you're new to Python/Restler/Backbone browse the code to see how it all works. I used **Eclipse** to build and debug the app. See **useful resources** below for setup details.   

Implementation details:
-----------------------
* Properties are represented in the system by the `Property` class defined in `pbo/property.php`.  
* The `Property` class was deliberately designed to allow easy conversion to a **REST**ful API. [Restler](http://luracast.com/products/restler/) uses `get`, `put`, `post`, and `delete` to map PHP methods to respective HTTP methods.  
* These four methods can be found in the `Base` class in `pbo/base.php`. This class can be extended to represent any other object in the system - such as `Property`.  
* The `Property` class also contains methods for the validation of user input.  
* `api/index.php` is used to create the REST API endpoints. This file acts as a "wrapper" around `pbo/property.php` so that `Property` objects are exposed via REST URLs. This [Restler example](http://help.luracast.com/restler/examples/_006_crud/readme.html) explains the mechanism in greater detail.
* `api/.htaccess` re-writes the URL to a friendlier format: ie: from `http://.../restler-backbone-example/api/property/index.php/1`, say, to `http://.../restler-backbone-example/api/property/1`.
* The `lib` folder contains some PHP helper scripts.   
* The `vendor` folder contains the Restler code. This was directly copied from the unzipped download. No modifications were necessary.  
* The `app/lib` folder contains open source javascript utilities. No modifications were necessary. Replace these files with their latest versions if so desired. Besides Backbone:  
	* [jQuery](http://jquery.com/) is a fast and concise JavaScript Library that simplifies HTML document traversing, event handling, animating, and Ajax interactions for rapid web development.  
	* [Underscore.js](http://underscorejs.org/) is a utility-belt library for JavaScript.  
	* [json2.js](https://github.com/douglascrockford/JSON-js) is for JSON in JavaScript.   
	* [mustache.js](https://github.com/janl/mustache.js/) is for minimal templating in JavaScript.   

TODO list:
----------
In this app, a lot of functionality was stripped away to highlight the relevant areas. To make this a "well-rounded" application, the following will need to be done:  
   
* Set up an authentication framework.  
* Set up a test framework.  
* More Documentation.  
* Page the retrieved data.  
* Make the page responsive.  

Useful resources:
-----------------
* Browse this [older post](http://bit-taming.com/development/php-basic-authentication-template/) for development server setup details (for Mac OSX 10.6.8 **snow leopard**).  
* [PyDev](http://pydev.org/) is a python development framework. Following [these](http://www.youtube.com/watch?v=gCapULV-tPE) instructions for integrating PyDev with Eclipse helped to build `resources/build_table_script.py` described above.  
* The following python resources were useful:  
	* [Dictionaries and lists](http://www.diveintopython.net/native_data_types/index.html#odbchelper.dict)  
	* [Parsing a CSV file](http://docs.python.org/2/library/csv.html#csv-fmt-params)  
	* [List comprehensions](http://www.diveintopython.net/native_data_types/mapping_lists.html)  
* This [Restler example](http://help.luracast.com/restler/examples/_006_crud/readme.html) provided the basis for the data and business object layers. Luracast's [Restler 3.0](http://luracast.com/products/restler/) is an open source program for creating HTTP web service APIs from PHP classes.  
* [Here](http://documentcloud.github.com/backbone/#) is the Backbone.js GitHub repository. [This](http://localtodos.com/) example was very helpful in building the application you see here. The [annotated code](http://documentcloud.github.com/backbone/docs/todos.html) does a great job of explaining how Backbone works.  
* [Core Web Application Development with PHP and MySQL (Wandschneider)](http://books.google.com.au/books?vid=ISBN0131867164&redir_esc=y) provided the session and error-handling smarts behind the page.  









