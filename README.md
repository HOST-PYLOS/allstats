Install Guide Linux (MySQL)

Note: This guide expect that you've set up a Webserver with MySQL already! However this guide describe Allstats in combination with a MySQL server!
First grab the package from Googlecode
wget http://allstats.googlecode.com/files/Allstats-1.4.156.zip
Or if you use Subversion you can grab the code via
svn checkout http://allstats.googlecode.com/svn/trunk/ allstats-read-only
Note that you don't need to unzip the archive, since you get the files directly.
Then unzip the zip archive with the unzip command
unzip Allstats-1.4.156.zip
A new folder called Allstats-1.4.156 is created. Move that folder in your htdocs/ folder. (You should know were it is)
Use the mv command here:
mv Allstats-1.4.156 /opt/lampp/htdocs/dota
Note that this is just an example folder! Your htdocs folder can be in some other place!
Now change into the moved folder
cd /opt/lampp/htdocs/dota/
Now open the config.php file with your favorite editor, I use vim for example
vim config.php
The important settings are the MySQL settings in the file. Enter the correct information and save the file.
$host = 'MySQL IP usually localhost';
$username = 'username';
$password = 'password';
$databasename = 'database name of GHost++';
If you want to display the chatlog you need to specify the path to the replays folder. This can also be done in the config.php file.
Install Guide Windows (MySQL)

The instructions for Windows are similar.
First download the Allstats zip-archive from Googlepage.
Extract the files to the htdocs folder and change the name if you like.
Edit the config.php with Notepad, or your favorite Editor
Like in the Linux guide you have to enter your correct MySQL server correct username and password.
Save the file and it should work
