<?
include("downloadfileclass.inc");
$downloadfile = new DOWNLOADFILE("../$fn");
if (!$downloadfile->df_download()) echo "<script>alert('error downloading file $fn');</script>";
?>