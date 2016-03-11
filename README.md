#Place Framework
A minimalistic, independent micro PHP web framework

The following .htacceess file is needed in the root directory.  Add any subdirectories before /index.php if necessary.

	RewriteEngine on
	RewriteCond %{REQUEST_FILENAME} !-f
	RewriteCond %{REQUEST_FILENAME} !-d
	RewriteRule ^([^?]*)$ /index.php?path=$1 [NC,L,QSA]
