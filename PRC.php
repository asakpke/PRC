<?php
session_start();

class PRC
{
	protected $app = array(
		'name' => 'PHP RAD CRUD',
		'nameShort' => 'PRC',
		'desc' => 'PHP - PHP: Hypertext Preprocessor. RAD - Rapid Application Development. CRUD - Create, Read, Update and Delete',
		'version' => '0.1.3',
		'developer' => 'Aamir Shahzad',
		'path' => '/opt/lampp/htdocs/PRC',
		'pathUrl' => 'http://localhost/PRC',
		// 'isSignup' => false,
		'isSignup' => true,
	);
	protected $dbConn;
	protected $tbl = array(
		'name',
		'nameUcF',
		'isPlural' => true,
		'cols' => true,
	);
	protected $showHTML = true;

	function __construct($opt = null)
	{
		// START - Redirect to login page if user is not logged-in
		if (empty($_SESSION['user']) and empty($opt['is login page'])) {
			$msg = 'Please login to view this page.';
			$color = 'red';
			header("Location: {$this->app['pathUrl']}/Login.php?msg={$msg}&color={$color}");
			exit;
		}
		// ENDED - Redirect to login page if user is not logged-in

		$this->dbConn = mysqli_connect(
			'localhost', // servername/IP
			'root', // username
			'', // password
			'example' // Database name, notice no ","
		);

		$this->tbl['nameUcF'] = ucfirst($this->tbl['name'] ?? false);

		if ($this->showHTML) {
			?>
			<!doctype html>
			<html lang="en">
			<head>
					<meta charset="utf-8">
					<meta name="viewport" content="width=device-width, initial-scale=1">
					<meta name="description" content="<?= $this->app['desc'] ?>">
					<meta name="author" content="<?= $this->app['developer'] ?>">
					<meta name="generator" content="<?= $this->app['nameShort'].' '.$this->app['version'] ?>">
					<title><?= $this->app['nameShort'].' - '.$this->app['name'] ?></title>
					<link rel="apple-touch-icon" sizes="180x180" href="<?= $this->app['pathUrl'] ?>/apple-touch-icon.png">
					<link rel="icon" type="image/png" sizes="32x32" href="<?= $this->app['pathUrl'] ?>/favicon-32x32.png">
					<link rel="icon" type="image/png" sizes="16x16" href="<?= $this->app['pathUrl'] ?>/favicon-16x16.png">
					<link rel="manifest" href="<?= $this->app['pathUrl'] ?>/site.webmanifest">
					<link href="<?= $this->app['pathUrl'] ?>/style.css?v=<?= $this->app['version'] ?>" rel="stylesheet">
			</head>
			<body>
			<header>
				<h1><a href="<?= $this->app['pathUrl'] ?>"><?= $this->app['name'] ?></a></h1>
			</header>
			<nav>
				<?php
				if (!empty($_SESSION['user'])) {
					$dir = $this->app['path'].'/';
					$all = array_diff(scandir($dir), ['.', '..', '.git', '.github']);

					foreach ($all as $ff) {
						if (is_dir($dir . $ff)) {
							echo "<a href=\"{$this->app['pathUrl']}/{$ff}\">{$ff}</a> | ";
						} // if is_dir
					} // foreach all

					echo '<a href="'.$this->app['pathUrl'].'/Logout.php">Logout</a>';
				}
				?>
			</nav>
			<?php
		} // if ($this->showHTML)
	} // __construct()

	function list()
	{
		$cols = $this->getCols('on listing');
		$sql = "SELECT {$this->tbl['name']}.id, {$cols['fields']} FROM {$this->tbl['name']};";
		$result = mysqli_query(
			$this->dbConn,
			$sql
		);

		if (mysqli_num_rows($result)) {
			$row = mysqli_fetch_assoc($result);
		} // if num_rows
		?>
		<main>
			<h2><?= $this->tbl['nameUcF'] ?> Records <a href="<?= $this->app['pathUrl'].'/'.$this->tbl['nameUcF'] ?>/Add.php" title="Add new record">+</a></h2>
			<?php
			showMsg();
			?>
			<table style="width:100%">
				<?php
				if (mysqli_num_rows($result)) {
					?>
					<thead>
						<tr>
							<?php
							echo $cols['headers'];
							echo '<th scope="col">Action</th>';
							?>
						</tr>
					</thead>
					<?php
				} // if num_rows
				?>
				<tbody>
					<?php
					if (mysqli_num_rows($result)) {
						do {
							?>
							<tr>
								<?php
								foreach ($this->tbl['cols'] as $key => $col) {
									if ($col['is display']['on listing'] === false) {
										continue;
									}
									
									echo "<td>{$row[$key]}</td>";
								} // foreach (tbl['cols'])

								echo "<td>
									<a href=\"{$this->app['pathUrl']}/{$this->tbl['nameUcF']}/View.php?id={$row['id']}\">View</a> |
									<a href=\"{$this->app['pathUrl']}/{$this->tbl['nameUcF']}/Edit.php?id={$row['id']}\">Edit</a> | 
									<a href=\"{$this->app['pathUrl']}/{$this->tbl['nameUcF']}/Delete.php?id={$row['id']}\">Delete</a>
								</td>";
								?>
							</tr>
							<?php
						} while ($row = mysqli_fetch_assoc($result));
					} // if num_rows
					else {
						?>
						<tr><th colspan="<?= $cols['count'] ?>" style="text-align:center;">No record found</th></tr>
						<?php
					} // else/if num_rows
					?>
				</tbody>
			</table>
		</main>
		<?php
	} // list()

	function view($id)
	{
		$cols = $this->getCols('on view');

		$sql = "SELECT {$cols['fields']} FROM {$this->tbl['name']} WHERE id = $id;";

		$result = mysqli_query(
			$this->dbConn,
			$sql
		);

		if (mysqli_num_rows($result)) {
			$row = mysqli_fetch_assoc($result);
		} // if num_rows
		?>
		<main>
			<h2><?= $this->tbl['nameUcF'] ?> Record</h2>
			<?php
			foreach ($this->tbl['cols'] as $key => $col) {
				if ($col['is display']['on view'] === false) {
					continue;
				}

				echo "<h3>{$col['display as']}</h3>";
				echo "<p>{$row[$key]}</p>";
			} // foreach (tbl['cols'])
			?>
		</main>
		<?php
	} // view()

	function add()
	{
		if (!empty($_POST)) {
			$cols = $this->getCols('on add');
			$values = '';
			$values = str_pad($values, $cols['count']*2-1, '?,'); // -1 to remove last ,

			$sql = "INSERT INTO {$this->tbl['name']}({$cols['fields']}) VALUES ({$values});";
			
			$stmt = mysqli_prepare(
				$this->dbConn,
				$sql
			);

			$values = '';
			$values = str_pad($values, $cols['count'], 's');

			mysqli_stmt_bind_param(
				$stmt,
				$values, // i	int, d	float, s string
				...array_values($_POST),
			);

			mysqli_stmt_execute($stmt);

			$msg = "{$this->tbl['nameUcF']} record added successfully.";
			$color = 'green';
			$loc = "{$this->app['pathUrl']}/{$this->tbl['nameUcF']}/index.php?msg={$msg}&color={$color}";
			header("Location: $loc");
			exit;
		} // if post
		?>
		<main>
			<h2>Add <?= $this->tbl['nameUcF'] ?> Record</h2>
			<form method="post">
				<?php
				foreach ($this->tbl['cols'] as $key => $col) {
					if ($col['is display']['on add'] === false) {
						continue;
					}
					?>
					<div>
						<input
							type="<?= $col['type'] ?>"
							name="<?= $key ?>"
							id="<?= $key ?>"
							placeholder="Enter <?= $key ?>"
							value="<?= $_POST[$key] ?? '' ?>"
							<?= $col['is required'] ? 'required' : '' ?>
						>
						<label for="<?= $key ?>"><?= $col['display as'] ?></label>
					</div>
					<?php
				} // foreach
				?>
				<br>
				<button class="btn btn-primary py-2" type="submit">Add <?= $this->tbl['nameUcF'] ?> Record</button>
			</form>
		</main>
		<?php
	} // add()

	function edit($id)
	{
		if (!empty($_POST)) {
			$set = '';

			foreach ($_POST as $key => $value) {
				$set .= "$key = ?, ";
			}

			$set = rtrim($set,', ');

			$sql = "UPDATE {$this->tbl['name']} SET $set WHERE id = $id;";
			
			$stmt = mysqli_prepare(
				$this->dbConn,
				$sql
			);

			$values = '';
			$values = str_pad($values, count($_POST), 's');
			
			mysqli_stmt_bind_param(
				$stmt,
				$values, // i	int, d	float, s string
				...array_values($_POST),
			);
			
			mysqli_stmt_execute($stmt);

			$msg = "{$this->tbl['nameUcF']} record updated successfully.";
			$color = 'green';
			$loc = "{$this->app['pathUrl']}/{$this->tbl['nameUcF']}/index.php?msg={$msg}&color={$color}";
			header("Location: $loc");
			exit;
		} // if post
		else {
			$cols = $this->getCols('on edit');

			$sql = "SELECT {$this->tbl['name']}.id, {$cols['fields']} FROM {$this->tbl['name']} WHERE id = $id LIMIT 1;";
			$result = mysqli_query(
				$this->dbConn,
				$sql
			);

			if (mysqli_num_rows($result)) {
				$row = mysqli_fetch_assoc($result);
			} // if num_rows
		} // else/if post
		?>
		<main>
			<h2>Edit <?= $this->tbl['nameUcF'] ?> Record</h2>
			<form method="post">
				<?php
				foreach ($this->tbl['cols'] as $key => $col) {
					if ($col['is display']['on add'] === false) {
						continue;
					}
					?>
					<div>
						<input
							type="<?= $col['type'] ?>"
							name="<?= $key ?>"
							id="<?= $key ?>"
							placeholder="Enter <?= $key ?>"
							value="<?= !empty($_POST[$key]) ? $_POST[$key] : $row[$key] ?>"
							<?= $col['is required'] ? 'required' : '' ?>
						>
						<label for="<?= $key ?>"><?= $col['display as'] ?></label>
					</div>
					<?php
				} // foreach
				?>
				<br>
				<button class="btn btn-primary py-2" type="submit">Update <?= $this->tbl['nameUcF'] ?> Record</button>
			</form>
		</main>
		<?php
	} // edit()

	function delete($id)
	{
		$result = mysqli_query(
			$this->dbConn,
			"DELETE FROM {$this->tbl['name']} WHERE id = $id;"
		);

		if ($result) {
			$msg = "{$this->tbl['nameUcF']} record deleted successfully.";
			$color = 'green';
		} // if result
		else {
			$msg = "Error in deleting {$this->tbl['nameUcF']} record.";
			$color = 'red';
		}
		
		$loc = "{$this->app['pathUrl']}/{$this->tbl['nameUcF']}/index.php?msg={$msg}&color={$color}";
		header("Location: $loc");
		exit;
	} // delete()

	function blob($id, $name)
	{
		$stmt = mysqli_prepare(
			$this->dbConn,
			"SELECT {$this->tbl['name']}.$name FROM {$this->tbl['name']} WHERE {$this->tbl['name']}.id = ? LIMIT 1;"
		);
		mysqli_stmt_bind_param($stmt, "i", $id);

		if (mysqli_stmt_execute($stmt) === true) {
			$result = mysqli_stmt_get_result($stmt);
			$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
			$finfo = new finfo(FILEINFO_MIME_TYPE);
			$mimeType = $finfo->buffer($row[$name]);
			header("Content-type: $mimeType");
			echo $row[$name];
		} // if (mysqli_stmt_execute($stmt) === true)
	} // blob()

	function signup()
	{
		if ($this->app['isSignup'] === false) {
			$msg = 'Signup disabled.';
			$color = 'red';
			header("Location: {$this->app['pathUrl']}/Login.php?msg={$msg}&color={$color}");
			exit;
		}

		if (!empty($_POST)) {
			$stmt = mysqli_prepare(
				$this->dbConn,
				'INSERT INTO users VALUES (null, ?, ?, ?);'
			);
			
			mysqli_stmt_bind_param(
				$stmt,
				"sss", // i	int, d	float, s string
				$_POST['name'],
				$_POST['email'],
				md5($_POST['password']),
			);
			
			mysqli_stmt_execute($stmt);

			$msg = 'Signup successfully, please login.';
			$color = 'green';
			header("Location: {$this->app['pathUrl']}/Login.php?msg={$msg}&color={$color}");
			exit;
		} // if post
		?>
		<main>
      <form method="post">
        <h2 class="">Sign Up</h2>
				<div>
          <input name="name" type="name" id="name" placeholder="Name">
					<label for="name">Name</label>
        </div>
        <div>
          <input name="email" type="email" id="email" placeholder="name@example.com">
					<label for="email">Email address</label>
        </div>
        <div>
          <input name="password" type="password" class="form-control" id="password" placeholder="Password">
					<label for="password">Password</label>
        </div>
        <button type="submit">Sign Up</button>
      </form>
    </main>
		<?php
	} // signup()

	function login()
	{
		if (!empty($_POST)) {
			$stmt = mysqli_prepare(
				$this->dbConn,
				"SELECT * FROM users WHERE email=? AND password=? LIMIT 1"
			);

			$_POST['password'] = md5($_POST['password']);

			$stmt->bind_param(
				"ss", 
				$_POST['email'],
				$_POST['password']
			);

			if ($stmt->execute() === true) {
				$result = $stmt->get_result();
				$row = $result->fetch_assoc();

				if (!empty($row)) {
					$_SESSION['user'] = $row;
					$color = 'green';
					header("Location: {$this->app['pathUrl']}/index.php?msg={$msg}&color={$color}");
					exit();
				}
				else {
					$msg = 'Invalid email/password.';
					$color = 'red';
					header("Location: {$this->app['pathUrl']}/Login.php?msg={$msg}&color={$color}");
					exit();
				}
			} // if ($stmt->execute() === true)
		} // if post
		?>
		<main>
      <form method="post">
        <h2 class="">Login</h2>
				<?php
				showMsg();
				?>
        <div>
          <input name="email" type="email" id="email" placeholder="name@example.com">
					<label for="email">Email address</label>
        </div>
        <div>
          <input name="password" type="password" class="form-control" id="password" placeholder="Password">
					<label for="password">Password</label>
        </div>
        <button type="submit">Login</button>
				<?= $this->app['isSignup'] ? '<a href="Signup.php">Signup</a>' : '' ?>
      </form>
    </main>
		<?php
	} // login()

	function index()
	{
		?>
		<main>
			<p>Welcome back <?= $_SESSION['user']['name'] ?>.</p>
		</main>
		<?php
	} // index()

	function logout()
	{
		$_SESSION['user'] = null;
		$msg = 'You are logged out successfully.';
		$color = 'green';
		header("Location: {$this->app['pathUrl']}/Login.php?msg={$msg}&color={$color}");
		exit;
	} // logout()

	function getCols($on) {
		$cols['headers'] = '';
		$cols['fields'] = '';
		$cols['count'] = 0;

		foreach ($this->tbl['cols'] as $key => $col) {
			if ($col['is display'][$on] === false) {
				continue;
			}
			
			$cols['headers'] .= "<th scope=\"col\">{$col['display as']}</th>";
			$cols['fields'] .= "{$this->tbl['name']}.{$key}, ";
			$cols['count']++;
			// Maybe limit cols['headers']/values to 10 or some columns
		} // foreach (tbl['cols'])

		$cols['fields'] = rtrim($cols['fields'],', ');

		return $cols;
	}

	function __destruct() {
		if ($this->showHTML) {
				?>
				<footer>
					<h6><?= $this->app['nameShort'].' - '.$this->app['version'] ?> &copy; <?= date('Y') ?></h6>
				</footer>
				</body>
				</html>
				<?php
		} // if ($this->showHTML)

		mysqli_close($this->dbConn); // optional
	} // __destruct()
} // class PRC

function pr($value,$name=null)
{
	$count = is_countable($value) ? count($value) : null;

	echo "<pre><h1>Name: {$name}, <small>Count:{$count}</small></h1>";
	print_r($value);
	echo '</pre>';
} // pr()

function prd($value,$name=null)
{
	pr($value,$name);

	die("Self die");
} // prd()

function showMsg() 
{
	if (!empty($_GET['msg'])) {
		echo "<p style=\"color:{$_GET['color']}\">{$_GET['msg']}</p>";
	} // if GET msg
} // showMsg()