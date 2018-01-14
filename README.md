# INFINITO #


### The leading call intelligence platform . ###

* Version 1
* [LINK](https://github.com/harleybalo/infinito.git)

### SUMMARY ###

* This test may be completed in either PHP or Go, whichever you prefer, but please use only the standard library and no other frameworks or external packages (you are allowed to use the external MySQL driver package in Go).
* You are tasked with writing a program to process files that have been uploaded by clients.
* The program should run once per minute using cron. When run, it should discover any CSV files in the “uploaded” directory, parse the rows in the file, insert their contents into a MySQL database, and then move each CSV file to the “processed” directory.
* Clients are expected to upload files in the following format, and each file should be validated to ensure that format has been used

* To Set up, please see config/parameters.ini file for DB credentials