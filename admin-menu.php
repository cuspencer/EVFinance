<?php
session_start();

require 'header.php';
require 'left_nav.php';

echo "<DIV id=\"maincontent\" class=\"w3-container\">";

echo "<DIV class=\"w3-container\">";
echo "<H3><B>Administrative Tools</B></H3>";
echo "</DIV>";

echo "<DIV class=\"w3-container\">";
echo "<BR/><a href=\"\" class=\"w3-bar-item w3-button admin-menu-item\"><label class=\"material-icons admin-icons\">account_circle</label> Users </a>";
echo "<BR/><a href=\"\" class=\"w3-bar-item w3-button admin-menu-item\"><label class=\"material-icons admin-icons\">account_balance</label> Accounts </a>";
echo "<BR/><a href=\"category-admin.php\" class=\"w3-bar-item w3-button admin-menu-item\"><label class=\"material-icons admin-icons\">source</label> Categories </a>";
echo "<BR/><a href=\"set-passwords.php\" class=\"w3-bar-item w3-button admin-menu-item\"><label class=\"material-icons admin-icons\">vpn_key</label> Password Reset </a>";
echo "</DIV>";

echo "</DIV>";
require 'footer.php';
?>
