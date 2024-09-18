<?php /** @noinspection DuplicatedCode */
// Uncomment to turn on PHP error reporting
//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);

require_once ('setEnv.php');

/**
 * @throws Exception
 */

/**
 * define a function to be the global exception handler that
 * will fire if no catch block is found
 */
function exceptionHandler ($e) {
    if (defined("DEVELOPMENT") && DEVELOPMENT===true) {
        echo "<p>Query failed: ".$e->getMessage()."</p>\n";
    } else {
        echo "<p><strong>A problem occurred. Please try again later.</strong></p>";
        log_error($e);
    }
}
// now set the php exception handler to be the one above
set_exception_handler("exceptionHandler");

/**
 * define a function to be the global error handler, this will
 * convert errors into exceptions. (i.e. all errors will be treated as exceptions)
 * @throws ErrorException
 */
function errorHandler ($errno, $errstr, $errfile, $errline) {
// check error isn’t excluded by server settings
    if(!(error_reporting() & $errno)) {
        return;
    }
    throw new ErrorException($errstr, $errno, 0, $errfile, $errline);
}
// now set the php error handler to be the one above
set_error_handler("errorHandler");

// function to log error to a server file
function log_error ($e) {
    $fileHandle = fopen('/logs/errors.log', 'ab');
    $errorDate = date('Y-m-d H:i:s');
    $errorMessage = $e->getMessage();

    //removing any possible new line elements from the error message
    $toReplace = array("\r\n", "\n", "\r");
    $replaceWith = ' ';
    $errorMessage = str_replace($toReplace, $replaceWith, $errorMessage);
    fwrite($fileHandle,$errorDate." | ".$errorMessage.PHP_EOL);
}

// a function for database connection using PDO
function getConnection(): PDO {
    try {
        $connection = new PDO("mysql:host=$_ENV[DB_SERVER]; dbname=$_ENV[DB_NAME]", "$_ENV[DB_USER]", "$_ENV[DB_PASS]");
        $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $connection;
    } catch (Exception $e) {
        /** @noinspection PhpUnhandledExceptionInspection */
        throw new Exception("Connection error: ". $e->getMessage(), 0, $e);
    }
}

// a function to checks how long a user has been logged-in and logs them out every 30 mins for security purposes
function session_timeout_chk() {
    if (get_session('session-time') === null && check_login()) {
        set_session('session-time', time()); //checks if user is logged in and create session time if not yet done so
    } else if (get_session('session-time') !== null && (time() - get_session('session-time') > 1800)) {
        session_unset();
        session_destroy();// destroy session if its longer than 30 minutes

        // future opportunities - destroy session only user is "inactive" for 30mins rather than every 30mins to improve user experience
    }
}

// function that generates the form to edit event if an eventID is passed.
function getEventEditForm($eventID) :string {
    try {
        $dbConn = getConnection();
        $eventSql = "select eventID, eventTitle, eventDescription, e.catID, catDesc, e.venueID, venueName, location, eventStartDate, eventEndDate, eventPrice from EGN_events e join EGN_categories c on e.catID = c.catID join EGN_venues v on e.venueID = v.venueID where eventID = :eventID";
        $eventQuery = $dbConn->prepare($eventSql);
        $eventQuery->execute(array(':eventID'=>$eventID));
        $event = $eventQuery->fetchObject();

        $eventName = $event->eventTitle;
        $eventDesc = $event->eventDescription;
        $catID = $event->catID;
//        $catName = $event->catDesc;
        $venueID = $event->venueID;
//        $venueName = $event->venueName;
//        $location = $event->location;
        $startDate = $event->eventStartDate;
        $endDate = $event->eventEndDate;
        $price = $event->eventPrice;

        //querying the category list to dynamically show in the <option> list.
        $catListSql = "select catID, catDesc from EGN_categories";
        $catListQuery = $dbConn->query($catListSql);
        $catListContent = "";

        while ($category = $catListQuery->fetchObject()) {
            $catSelected = ($catID === $category->catID) ? "selected" : "";
            $catListContent .= "<option value='$category->catID' $catSelected>$category->catDesc</option>\n";
        }

        //querying the venue list and location to dynamically show in the <option> list.
        $venueListSql = "select venueID, venueName, location from EGN_venues order by location";
        $venueListQuery = $dbConn->query($venueListSql);
        $venueListContent = "";

        while ($venue = $venueListQuery->fetchObject()) {
            $venueSelected = ($venueID === $venue->venueID) ? "selected" : "";
            $venueListContent .= "<option value='$venue->venueID' $venueSelected>$venue->location - $venue->venueName</option>\n";
        }
    } catch (Exception $e) {
        log_error($e);
        echo "<p>You have not selected a valid event to edit, please go to \"<a href='admin.php'>Manage Events</a>\" and choose an event to edit.</p>";
    }
    $formContent = <<< EDITFORM
    <form id="editEventForm" method="post" action="editProcess.php">
        <div class="formContainer">
            <label for="eventID">Event ID</label>
            <input type="text" id="eventID" name="eventID" value="$eventID" readonly>
            <label for="eventName">Event Name</label>
            <input type="text" id="eventName" name="eventName" value="$eventName" aria-required="true" accesskey="n">
            <label for="eventDesc">Event Description </label>
            <textarea id="eventDesc" name="eventDesc" rows="5" accesskey="d">$eventDesc</textarea>
            <label for="catID">Category</label>
            <select id="catID" name="catID" aria-required="true" accesskey="c">
                $catListContent
            </select>
            <label for="venueID">Venue</label>
            <select id="venueID" name="venueID" aria-required="true" accesskey="v">
                $venueListContent
            </select>
            <label for="startDate">Event Start Date</label>
            <input type="date" id="startDate" name="startDate" value="$startDate" aria-required="true" accesskey="s">
            <label for="endDate">Event End Date</label>
            <input type="date" id="endDate" name="endDate" value="$endDate" aria-required="true" accesskey="e">
            <label for="price">Event Price</label>
            <input type="number" id="price" name="price" step=".01" value="$price" aria-required="true" accesskey="p">
            <input type="submit" value="Edit Event" accesskey="d">
        </div>
    </form>
    EDITFORM;
    $formContent .= "\n";
    return $formContent;
}

// function that generates a login form
function getLoginForm($redirectedFrom=null): string {
    // takes a redirection parameter if the user was taken to login from somewhere else so that the function can send them back to where they came from (user experience)
    $redirectedFrom = $_GET['from'] ?? null;
    if (!empty($redirectedFrom)) {
        $getRedirection = "?from=$redirectedFrom";
    } else {
        $getRedirection = null;
    }
    $formContent = <<<LOGINFORM
    <form id="loginForm" action="authProcess.php$getRedirection" method="post">
                <div class="formContainer">
                    <label for="username">Username</label>
                    <input type="text" name="username" id="username" aria-required="true" accesskey="u"> <span class ="warning" id="usernameWarning"></span>
                    <label for="password">Password</label>
                    <input type="password" name="password" id="password" aria-required="true" accesskey="p"> <span class ="warning" id="passwordWarning"></span>
                    <input type="submit" value="Log in" accesskey="s">
                </div>
            </form>
    LOGINFORM;
    $formContent .= "\n";
    return $formContent;
}

// function to run a series of validation to check updates made by user on any events
function validate_updates(): array {
    $input = array();
    $errors = array();

    // checking if any data was passed at all to prevent possible direct access to the processing page
    if (empty($_POST)){
        $errors[] = "You have not chosen an event to edit.";
    } else {
        $input['eventID'] = trim($_POST['eventID']) ?? null;
        $input['eventName'] = trim($_POST['eventName']) ?? null;
        $input['eventDesc'] = trim($_POST['eventDesc']) ?? null;
        $input['catID'] = trim($_POST['catID']) ?? null;
        $input['venueID'] = trim($_POST['venueID']) ?? null;
        $input['startDate'] = trim($_POST['startDate']) ?? null;
        $input['endDate'] = trim($_POST['endDate']) ?? null;
        $input['price'] = trim($_POST['price']) ?? null;

        // regular expression pattern for date validation
        $dateRegex = array('options' => array('regexp' => '/^[0-9]{4}-[01][0-9]-[0-3][0-9]$/'));

        if (empty($input['eventID'])) {
            $errors[] = "You have not chosen an event to edit.";
        } else {
            if (empty($input['eventName'])) {
                $errors[] = "Event name can't be empty!";
            } elseif (strlen($input['eventName']) > 256) {
                $errors[] = "Event name can't be more than 256 characters.";
            }

            if (!empty($input['eventDesc'])) {
                if (strlen($input['eventDesc']) > 512) {
                    $errors[] = "Event description can't be more than 512 characters.";
                }
            }

            if (empty($input['catID'])) {
                $errors[] = "Category is missing!";
            } else {
                // preventing data manipulation by double-checking with category database
                try {
                    $catIDArr = array();
                    $dbConn = getConnection();
                    $catIdSQL = "select catID from EGN_categories";
                    $catIdQuery = $dbConn->query($catIdSQL);
                    while ($validCatID = $catIdQuery->fetchObject()) {
                        $catIDArr[] = $validCatID->catID;
                    }
                } catch (Exception $e) {
                    echo "<p>Error: ".$e->getMessage();
                }
                if (!in_array($input['catID'],$catIDArr)) {
                    $errors[] = "Selected category is not valid.";
                }
            }

            if (empty($input['venueID'])) {
                $errors[] = "Venue is missing!";
            } else {
                // preventing data manipulation by double-checking with venue database
                try {
                    $venueIDArr = array();
                    $dbConn = getConnection();
                    $venueIdSQL = "select venueID from EGN_venues";
                    $venueIdQuery = $dbConn->query($venueIdSQL);
                    while ($validVenueID = $venueIdQuery->fetchObject()) {
                        $venueIDArr[] = $validVenueID->venueID;
                    }
                } catch (Exception $e) {
                    echo "<p>Error: ".$e->getMessage();
                }
                if (!in_array($input['venueID'],$venueIDArr)) {
                    $errors[] = "Selected venue is not valid.";
                }
            }

            if (empty($input['startDate'])) {
                $errors[] = "Start date can't be empty!";
            } elseif (!filter_var($input['startDate'], FILTER_VALIDATE_REGEXP,$dateRegex)) {
                $errors[] = "Please provide a valid start date.";
            }

            if (empty($input['endDate'])) {
                $errors[] = "End date can't be empty!";
            } elseif (!filter_var($input['endDate'], FILTER_VALIDATE_REGEXP,$dateRegex)) {
                $errors[] = "Please provide a valid end date.";
            }

            if (!empty($input['startDate']) and !empty($input['endDate'])) {
                $startDate = date_create($input['startDate']);
                $endDate = date_create($input['endDate']);
                $dateDiff = date_diff($startDate,$endDate); // calculation of date difference between start and end dates
                if ($dateDiff->format("%R%a") < 0) {
                    $errors[] = "Event end date must not be earlier than the start date.";
                }
            }

            if (empty($input['price'])) {
                $errors[] = "Price can't be empty!";
            } else if ($input['price'] < 0 or $input['price'] > 99.99) {
                $errors[] = "Price must be between 0 and 99.99";
            }
        }
    }

    // establish a connection if there is no error
    if (!$errors) {
        try {
            $dbConn = getConnection();
            $updateEventSql = "update EGN_events set eventTitle = :eventTitle, eventDescription = :eventDescription, venueID = :venueID, catID = :catID, eventStartDate = :startDate, eventEndDate = :endDate, eventPrice = :price where eventID = :eventID";
            // making data safe by preparing input before updates
            $updateEventQuery = $dbConn->prepare($updateEventSql);
            $updateEventQuery->execute(array(':eventID' => $input['eventID'],
                ':eventTitle' => $input['eventName'],
                ':eventDescription' => $input['eventDesc'],
                ':venueID' => $input['venueID'],
                ':catID' => $input['catID'],
                ':startDate' => $input['startDate'],
                ':endDate' => $input['endDate'],
                ':price' => $input['price']));
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage();
        }
    }

    if ($errors && empty($input['eventID'])) {
        $errors[] = "Please <a href='admin.php'>choose an event</a> first to make changes."; // checking if an event ID is specified at the first place
    } else if ($errors) {
        $errors[] = "One or more error found. Please <a href='editEvent.php?eventID=".$input['eventID']."'>go back</a> and check again.";
    }
    return array($input, $errors);
}

// function to run a series of validation to check user login credentials
function validate_login(): array {
    $input = array();
    $errors = array();

    $input['username'] = trim($_POST['username']) ?? null;
    $input['password'] = $_POST['password'] ?? null;
    if (empty($input['username'])) {
        $errors[] = "Username can't be empty!";
    } elseif (empty($input['password'])) {
        $errors[] = "Password can't be empty!";
    }

    // establish a database connection if there is no error
    if (!$errors) {
        try {
            $dbConn = getConnection();
            $userSql = "select firstname, passwordHash from EGN_users where username = :username";
            // sanitising on user's manual input using prepare
            $userQuery = $dbConn->prepare($userSql);
            $userQuery->execute(array(':username' => $input['username']));
            $user = $userQuery->fetchObject();


            $passwordHash = ($user) ? $user->passwordHash : null;

            if (password_verify($input['password'], $passwordHash)) {
                set_session('logged-in',true);
                set_session('firstname',$user->firstname);
            } else {
                $errors[] = "Username or password is incorrect!";
            }
        } catch (Exception $e) {
            echo "Error: ". $e->getMessage();
        }
    }
    if ($errors) {
        $errors[] = "Please check your credentials and <a href='login.php'>log in</a> again.";
    }
    return array($input, $errors);
}

// a function to show validation errors
function show_errors($errors): string {
    $output = '';
    foreach ($errors as $error) {
        $output .= "<p>".$error."</p>\n";
    }
    return $output;
}

// handy function to set session keys and values
function set_session($key, $value): bool {
    $_SESSION[$key] = $value;
    return true;
}

// handy function to get session data
function get_session($key) {
    return $_SESSION[$key] ?? null;
}

// a function to check if a user's login status
function check_login(): bool {
    //strictly setting to identically match specified logged-in value, in this case 'true' so that the if condition for checking the session will not be manipulated just by having some gibberish value
    if (get_session('logged-in') === true) {return true;}
    else {return false;}
}

// HTML Page Component Functions
function generateHeader($showLogin=null): string {
    // will hide login button and admin wrapper in general to improve UI on certain pages that deals with authentication
    $showLogin = ($showLogin === "hide-log-in") ? "hide" : null;
    $adminBtnText = check_login() ? "Log Out" : "Log In";
    $adminBtnLink = "authProcess.php";

    // getting user's first name from session data if user is logged in, and feeding it into HTML content using $welcomeLine.
    $userFirstname = (check_login() && !empty(get_session("firstname"))) ? get_session("firstname") : null;
    $welcomeLine = isset($userFirstname) ? "<span class='welcome-text'>Hello, $userFirstname!</span>" : "";
    $headerContent = <<<HEADER
    <header>
        <a href="index.php">
        <h1 id="siteTitle" hidden>Great Events North</h1>
        <img id="siteLogo" src="egn-logo.png" alt="website logo">
        </a>
        <div class="admin-wrapper $showLogin">
            $welcomeLine
            <a class="admin-btn" href="$adminBtnLink">$adminBtnText</a>
        </div>
    </header>
    HEADER;
    $headerContent .= "\n";
    return $headerContent;
}

// function to generate a banner picture, defaults are set
function generateBanner($imgSrc="banner.png",$altTxt="Newcastle City Centre"): string{
    $bannerContent = <<< BANNER
    <div class="banner">
        <img src="$imgSrc" alt="$altTxt">
    </div>
    BANNER;
    $bannerContent .= "\n";
    return $bannerContent;
}

// generates a navigation bar with current page parameter set to 'active' class styling
function generateMainNav($currentPage=null): string{
    $navList = null;
    $pages = array(
        'index.php'=>'Home',
        'bookEventsForm.php'=>'Book Events',
        'credits.php'=>'Credits');

    foreach ($pages as $link => $name) {
        $isActive = ($link === $currentPage) ? "class = 'active'" : null;
        $navList .= "<li><a href='$link' $isActive>$name</a></li>\n\t\t";
    }
    $isAdminActive = ($currentPage === 'admin') ? "class = 'active'" : null;

    $adminNavContent = <<< ADMINNAV
    <nav id="adminNav">
        <ul>
            <li><a href="admin.php" $isAdminActive>Manage Events</a></li>
        </ul>
    </nav>
    ADMINNAV;
    $adminNav = check_login() ? $adminNavContent : "";
    $navContent = <<< NAVMENU
    <div class="mainNav">
        <nav id="userNav">
        <ul>
            $navList
        </ul>
        </nav>
        $adminNav
    </div>
    
    NAVMENU;
    $navContent .= "\n";
    return $navContent;
}

function generateFooter(): string {
    // getting current year to update the year of copyright automatically in the HTML
    $currentYear = date("Y");
    $footerContent = <<<FOOTER
    <footer>
        <p>Copyright &#169; $currentYear Events Guide North. All rights Reserved.</p>
    </footer>
    FOOTER;
    $footerContent .= "\n";
    return $footerContent;
}