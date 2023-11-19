<img src="sentinel/images/logo.png" alt="Cyber Sentinel logo" width="450">

# Cyber Sentinel
## `I believe most of the vulnerabilities, not just the technical ones, can be patched with proper requirements specification.`<br>
Cyber Sentinel is a PHP/MySQL web application made intentionally vulnerable! This project aims to increase awareness about common yet dangerous vulnerabilities.<br>
This application allows the user to exploit some beginner-level vulnerabilities by themselves. Tutorials are provided to learn about:
- The vulnerability 
- How it can be exploited 
- The part of the code causing this vulnerability.
- How to patch this vulnerability at different levels.
<hr>

### For users
There are different vulnerabilities with different difficulty levels. The difficulty levels are based on how good the patch is for that specific vulnerability. There is no fixed objective to complete a module. If you feel you've exploited the system thoroughly, the goal is reached! There is a help button at the bottom to view hints & tips for that vulnerability. There are additional links for further reading on each vulnerability.<br>
To set up this application on Windows, you must run Apache and MySQL services, which are inbuilt in XAMPP. Follow this for setting up Cyber Sentinel on [Windows](https://suchitreddi.github.io/cybersentinel/docs/setupw.html). After setting up and downloading the repository, rename the folder to `cybersentinel` and place it inside htdocs<br>
You can also download the dockerized version of the application here, which will require you to download the Docker Desktop application for Windows. Follow the steps in this Docker Hub repository to use this application on [Docker](https://hub.docker.com/r/5herl0ck/cybersentinel).<hr>

### For developers
- Every module follows a similar template, which is very easy to understand.
- All pages refer to this master or layout page `/sentinel/includes/sentinelPage.inc.php` containing most of the HTML front-end code, which is reused.
- Adding new modules is simple. Add your folder under vulnerabilities, and then add its base page to the vulnerabilities drop-down list in the master page mentioned above.
- To change the front-end CSS, you can edit `/sentinel/css/main.css`.
- You can find the documents for the project [here](https://suchitreddi.github.io/cybersentinel/cybersentinel.html).
<hr>

## Disclaimer
I do not take responsibility for how anyone uses this application (Cyber Sentinel). I have made the purposes of the application clear, and it should not be used maliciously. I have warned and taken measures to prevent users from installing Cyber Sentinel on live web servers.<br>
If any web server is compromised via installing Cyber Sentinel, it is not my responsibility. It is the responsibility of the person/s who uploaded and installed it.<br>
This application is vulnerable! There are documented vulnerabilities that will cause more undocumented ones. So, it is strictly advised `not` to use it directly on your host machine without any virtual machine or docker. Do not host code from this project on internet-facing servers, as that will compromise them.<br><hr>

Feel free to highlight any mistakes and contribute to this project by opening pull requests. I'm free for a quick chat at `suchit20016@gmail.com`

## Credits
This project is inspired from [DVWA](https://github.com/digininja/DVWA). I added more functionalities and improved the user interface on top of this amazing project.
