<!DOCTYPE html>
<html>
<head lang="en">
    <meta charset="UTF-8">
    <title>sKrapper</title>
    <link rel="stylesheet" type="text/css" href="assets/custom.css">
</head>
<body>
<div style="text-align: center;"><h2>Search Data From Amazon</h2></div>
<form  id="searchForm" action="data_fetch.php" method="post" style="text-align: center">
    <input type="text" name="keyword">
    <input type="submit" name="submit" value="Submit">
</form>
<pre id="result"></pre>
<script src="https://code.jquery.com/jquery-2.1.4.min.js"></script>
<script src="assets/custom.js"></script>
</body>

</html>