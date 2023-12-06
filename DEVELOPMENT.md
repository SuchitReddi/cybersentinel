<b> This project is based on Damn Vulnerable Web Application (DVWA). You can find the original project here: https://github.com/digininja/DVWA </b>  

<b> What's different from DVWA? </b>  
- Included few basic vulnerabilities like Command Injection, SQL Injection.  
- Changed the UI drastically.  
- Added tutorials onsite to guide users better.  
- Added user signup and deletion.
- Added logging functionality.
- Modified the database to include the above functionalities.
- A few small and crude changes here and there.

<b> What can be done? </b>
- This project was rushed, refining is required. Unrequired parts should be removed.
- Remaining vulnerabilities from DVWA, and newer vulnerabilities should be added.
- Tutorials should be prepared for all added vulnerabilities.
- Some existing functionalities like view help and source buttons in each vulneraility stopped working. They should be fixed.
- Any new ideas and improvements are welcome.
- I will prepare a proper usage guide to help contributors as soon as I am free.

<b> How to add new vulnerabilities? </b>
- Every module follows a similar template, which is very easy to understand. See the vulnerabilities folder for the basic structure.
- All pages refer to this master or layout page `/sentinel/includes/sentinelPage.inc.php` containing most of the HTML front-end code, which is reused.
- Adding new modules is simple. Add your source code folder in "vulnerabilities", and then add the vulnerability's index page to the vulnerabilities drop-down in master page mentioned above.
- To change the front-end CSS, you can edit `/sentinel/css/main.css`.
- You can find the documents for the project [here](https://suchitreddi.github.io/cybersentinel/cybersentinel.html).
