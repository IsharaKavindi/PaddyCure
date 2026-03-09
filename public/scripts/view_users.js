function deleteUser(userId) {
	const isDeleting = confirm('Are you sure you want to delete this user?');
	if (!isDeleting) return;

	fetch(`view_users.php`, {
		method: 'POST',
		headers: {
			'Content-Type': 'application/x-www-form-urlencoded',
		},
		body: `reason=delete_user&user_id=${userId}`,
	}).then(async (response) => {
		alert('User deleted successfully');
		location.reload();
	});
}
