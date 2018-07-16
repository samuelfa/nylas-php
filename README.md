## Nylas PHP

PHP bindings for the Nylas REST API [https://www.nylas.com](https://www.nylas.com)

## Modification
I fork this project from lanlin/nylas-php, because i find the original package only support guzzle 5 and lanlin package only supports PHP 7.
Now i made some change, so it can support guzzle >6.0.

Be care, there maybe has some bugs that i have not found yet.


## Installation

You can install my fork library with composer.

```php
composer require "sumacrm/nylas-php"
```


## Usage

The Nylas REST API uses server-side (three-legged) OAuth, and this library provides convenience methods to simplify the OAuth process. Here's how it works:

1. You redirect the user to our login page, along with your App Id and Secret
1. Your user logs in
1. She is redirected to a callback URL of your own, along with an access code
1. You use this access code to get an authorization token to the API

For more information about authenticating with Nylas, visit the [Developer Documentation](https://www.nylas.com/docs/gettingstarted-hosted#authenticating).

In practice, the Nylas REST API client simplifies this down to two steps.

## Options Parameters

```php
$OPTIONS =
[
    'app_id'     => 'your app id',
    'app_secret' => 'your app secret',
    'app_server' => 'default https://api.nylas.com/',

    'token'      => 'token',
    'debug'      => 'true or false',
];
```

## Auth

**index.php**

```php
$client = new Nylas($OPTIONS);

$redirect_url = 'http://localhost:8080/login_callback.php';

$get_auth_url = $client->oauth($OPTIONS)->createAuthURL($redirect_url);

// redirect to Nylas auth server
header("Location: ".$get_auth_url);
```

**login_callback.php**

```php
$access_code = $_GET['code'];

$client = new Nylas($OPTIONS);

$get_token = $client->oauth($OPTIONS)->getAuthToken($access_code);

// save token in session
$_SESSION['access_token'] = $get_token;
```


## Fetching Threads

```php
// init client!
$client = new Nylas($OPTIONS);

// Fetch the first thread
$first_thread = $client->threads($OPTIONS)->first();

echo $first_thread->id;

// Fetch first 2 latest threads
$two_threads = $client->threads($OPTIONS)->all(2);

// Fetch threads from offset 30, and limit 50
$part_threads = $client->threads($OPTIONS)->part(30, 50);

foreach($two_threads as $thread)
{
    echo $thread->id;
}

// List all threads with 'ben@nylas.com'
$search_criteria = array("any_email" => "ben@nylas.com");

$get_threads = $client->threads($OPTIONS)->where($search_criteria)->items()

foreach($get_threads as $thread)
{
    echo $thread->id;
}
```

## Working with Threads

```php
// List thread participants
foreach($thead->participants as $participant)
{
    echo $participant->email;
    echo $participant->name;
}

// Unread or Read
$thread->unread($id, $unread = false);

// Move to folder
$thread->move($id, $target_id, $type = 'folder');

// Star
$thread->starred($id, $starred = true);

// Listing messages
foreach($thread->messages()->items() as $message)
{
    echo $message->subject;
    echo $message->body;
}
```

## Working with Files


```php
$client = new Nylas($OPTIONS);

$file_path = '/var/my/folder/test_file.pdf';

$upload_resp = $client->files($OPTIONS)->create($file_path);

echo $upload_resp->id;
```

## Working with Drafts

```php
$client = new Nylas($OPTIONS);

$message =
[
     "to"      =>
     [
         ["name" => "Nylas", "email" => "nylas@nylas.com"],
         ["name" => "Goole", "email" => "goole@google.com"],
     ],
     "subject" => "Hello, PHP!",
     "body"    => "Test <br> message"
];

$draft = $client->drafts($OPTIONS)->create($message_obj);

$send_message = $draft->send( ['id' => $draft->id] );

echo $send_message->id;
```

## Working with Send Directly

Carefully, send directly is diffrent from create a draft and then send it above.

```php
$client = new Nylas($OPTIONS);

$message =
[
     "to"      =>
     [
         ["name" => "Nylas", "email" => "nylas@nylas.com"],
         ["name" => "Goole", "email" => "goole@google.com"],
     ],
     "subject" => "Hello, PHP!",
     "body"    => "Test <br> message"
];

$draft = $client->drafts($OPTIONS)->send($message_obj);
```

## Working with Events

```php
$client = new Nylas($OPTIONS);

$calendars = $client->calendars($OPTIONS)->all();

$calendar = null;

foeach($calendars as $i)
{
    if(!$i->read_only) { $calendar = $i; }
}

$calendar_data =
[
    "title"        => "Important Meeting",
    "location"     => "Nylas HQ",
    "participants" => [ ["name" => "nylas", "email" => "nylas@nylas.com"] ]
    "calendar_id"  => $calendar->id,
    "when"         => array("start_time" => time(),
    "end_time"     => time() + (30*60))
];

// create event
$event = $client->events($OPTIONS)->create($calendar_data);

echo $event->id;

// update
$event = $event->update(array("location" => "Meeting room #1"));

// delete event
$event->delete();

// delete event (alternate)
$remove = $client->events($OPTIONS)->find($event->id)->delete();
```

## Webhooks Signature Verification

```php
$client = new Nylas($OPTIONS);
$is_valid = $client->xSignatureVerification($code, $data, $app_secret)
```

## End

This nylas-php project is forked by https://github.com/lanlin and by https://github.com/samuelfa (support for PHP 5.6)

it's not the official version. The official version has many bugs.

And not suport guzzle >6.0 yet. So i fork it, and made many modification.

Surely there's bugs in my fork, but i not have much time on this.

So, help fix these bugs, request pulls are welcome.

