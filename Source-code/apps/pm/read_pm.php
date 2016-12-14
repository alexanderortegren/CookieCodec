<?php
include('../dbconnect.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <title>Read a PM</title>
    </head>
    <body>

<!--NAVIGATION-->
<?php include_once('../../lib/header.php'); ?>

<?php
//We check if the user is logged
if(isset($_SESSION['usr_name']))
{
//We check if the ID of the discussion is defined
if(isset($_GET['id']))
{
$id = intval($_GET['id']);
//We get the title and the narators of the discussion
$req1 = mysqli_query($con,'SELECT title, user1, user2 FROM pm WHERE id="'.$id.'" AND id2="1"');
$dn1 = mysqli_fetch_array($req1);
//We check if the discussion exists
if(mysqli_num_rows($req1)==1)
{
//We check if the user have the right to read this discussion
if($dn1['user1']==$_SESSION['usr_id'] or $dn1['user2']==$_SESSION['usr_id'])
{
//The discussion will be placed in read messages
if($dn1['user1']==$_SESSION['usr_id'])
{
	mysqli_query($con,'UPDATE} pm SET user1read="yes" WHERE id="'.$id.'" AND id2="1"');
	$user_partic = 2;
}
else
{
	mysqli_query($con,'UPDATE pm SET user2read="yes" WHERE id="'.$id.'" AND id2="1"');
	$user_partic = 1;
}
//We get the list of the messages
$req2 = mysqli_query($con,'SELECT pm.timestamp, pm.message, users.id AS userid, users.username, users.avatar FROM pm, users WHERE pm.id="'.$id.'" AND users.id=pm.user1 ORDER BY pm.id2');
//We check if the form has been sent
if(isset($_POST['message']) and $_POST['message']!='')
{
	$message = $_POST['message'];
	//We remove slashes depending on the configuration
	if(get_magic_quotes_gpc())
	{
		$message = stripslashes($message);
	}
	//We protect the variables
	$message = mysqli_real_escape_string($con,nl2br(htmlentities($message, ENT_QUOTES, 'UTF-8')));
	//We send the message and we change the status of the discussion to unread for the recipient
	if(mysqli_query($con,'INSERT INTO pm (id, id2, title, user1, user2, message, timestamp, user1read, user2read)VALUES("'.$id.'", "'.(intval(mysqli_num_rows($req2))+1).'", "", "'.$_SESSION['usr_id'].'", "", "'.$message.'", "'.time().'", "", "")') and mysqli_query($con,'UPDATE pm SET user'.$user_partic.'read="yes" WHERE id="'.$id.'" and id2="1"'))
	{
?>
<div class="message">Your message has successfully been sent.<br />
<a href="read_pm.php?id=<?php echo $id; ?>">Go to the discussion</a></div>
<?php
	}
	else
	{
?>
<div class="message">An error occurred while sending the message.<br />
<a href="read_pm.php?id=<?php echo $id; ?>">Go to the discussion</a></div>
<?php
	}
}
else
{
//We display the messages
?>
<div class="content">
<h1><?php echo $dn1['title']; ?></h1>
<table class="messages_table">
	<tr>
    	<th class="author">User</th>
        <th>Message</th>
    </tr>
<?php
while($dn2 = mysqli_fetch_array($req2))
{
?>
	<tr>
    	<td class="author center"><?php
if($dn2['avatar']!='')
{
	echo '<img src="'.htmlentities($dn2['avatar']).'" alt="Image Perso" style="max-width:100px;max-height:100px;" />';
}
?><br /><a href="../profile_test/index.php?id=<?php echo $dn2['userid']; ?>"><?php echo $dn2['username']; ?></a></td>
    	<td class="left"><div class="date">Sent: <?php echo date('m/d/Y H:i:s' ,$dn2['timestamp']); ?></div>
    	<?php echo $dn2['message']; ?></td>
    </tr>
<?php
}
//We display the reply form
?>
</table><br />
<h2>Reply</h2>
<div class="center">
    <form action="read_pm.php?id=<?php echo $id; ?>" method="post">
    	<label for="message" class="center">Message</label><br />
        <textarea cols="40" rows="5" name="message" id="message"></textarea><br />
        <input type="submit" value="Send" />
    </form>
</div>
</div>
<?php
}
}
else
{
	echo '<div class="message">You dont have the rights to access this page.</div>';
}
}
else
{
	echo '<div class="message">This discussion does not exists.</div>';
}
}
else
{
	echo '<div class="message">The discussion ID is not defined.</div>';
}
}
else
{
	echo '<div class="message">You must be logged to access this page.</div>';
}
?>
		<div class="foot"><a href="list_pm.php">Go to my personnal messages</a> - <a href="http://www.webestools.com/">Webestools</a></div>
	</body>
</html>