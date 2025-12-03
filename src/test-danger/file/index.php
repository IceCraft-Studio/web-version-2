<?php
require "users.lib.php";
$action = isset($_REQUEST["action"]) ? $_REQUEST["action"] : null;
if ($action) {
	switch ($action) {
		case "add": add_user($_POST["name"], $_POST["email"], $_POST["avatar"]); break;
		case "edit": edit_user($_POST["id"], $_POST["name"], $_POST["email"], $_POST["avatar"]); break;
		case "delete": delete_user($_POST["id"]); break;
	}
	if ($action != "view") { header("Location: index.php"); }
}
?>

<!doctype html>
<html>
<head>
    <title>User Management</title>
	<style>
		body { font-family: sans-serif; }
		table { border-collapse: collapse;}
		td { border: 1px solid gray; }
		form { display: flex; flex-direction: column; align-items: start; }
	</style>
</head>
<body>

<h1>Users</h1>
<table>
    <thead>
        <tr>
            <th>Avatar</th>
            <th>Name</th>
            <th>E-mail</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach (list_users() as $user) { ?>
            <tr>
                <td><?php echo htmlspecialchars($user["avatar"]); ?></td>
                <td><?php echo htmlspecialchars($user["name"]); ?></td>
                <td><?php echo htmlspecialchars($user["email"]); ?></td>
                <td>
					<form action="index.php">
                        <input type="hidden" name="action" value="view">
                        <input type="hidden" name="id" value="<?php echo $user["id"]; ?>">
                        <button type="submit">Edit</button>
					</form>
                    <form action="index.php" method="post">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="id" value="<?php echo $user["id"]; ?>">
                        <button type="submit">Delete</button>
                    </form>
                </td>
            </tr>
        <?php } ?>
    </tbody>
</table>

<h2>Add User</h2>
<form action="index.php" method="post">
    <input type="hidden" name="action" value="add">
    <label>Avatar: <input name="avatar" maxlength="2" size="1"></label>
    <label>Name: <input name="name"></label>
    <label>E-mail: <input name="email"></label>
    <button type="submit">Add User</button>
</form>

<?php if ($action == "view") { ?>
    <?php
		$user = get_user($_GET["id"]);
    	if ($user) {
	?>
        <h2>Edit User</h2>
        <form action="index.php" method="post">
            <input type="hidden" name="action" value="edit">
            <input type="hidden" name="id" value="<?php echo $user["id"]; ?>">
            <label>Avatar: <input name="avatar" maxlength="2" size="1" value="<?php echo htmlspecialchars($user["avatar"]); ?>"></label>
            <label>Name: <input name="name" value="<?php echo htmlspecialchars($user["name"]); ?>"></label>
            <label>E-mail: <input name="email" value="<?php echo htmlspecialchars($user["email"]); ?>"></label>
            <button type="submit">Save Changes</button>
        </form>
    <?php } else { ?>
        <p>User not found.</p>
    <?php } ?>
<?php } ?>

</body>
</html>
