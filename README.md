## Nylas PHP

PHP bindings for the Nylas REST API [https://www.nylas.com](https://www.nylas.com)

## Modification
I fork this project, because i find the orignial package only suport guzzle 5.
Now i made some change, so it can support guzzle >6.0.

Be care, there maybe has some bugs that i have not found yet.


## Installation

You can install my fork library by add the following repository to your recomposer.json

```json
"repositories": [
    {
        "type": "git",
        "url": "https://github.com/lanlin/nylas-php.git"
    }
]
```

And then run following command in CLI

```php
composer require "nylas/nylas-php:1.0.0.7"
```


## Usage

The Nylas REST API uses server-side (three-legged) OAuth, and this library provides convenience methods to simplify the OAuth process. Here's how it works:

1. You redirect the user to our login page, along with your App Id and Secret
1. Your user logs in
1. She is redirected to a callback URL of your own, along with an access code
1. You use this access code to get an authorization token to the API

For more information about authenticating with Nylas, visit the [Developer Documentation](https://www.nylas.com/docs/gettingstarted-hosted#authenticating).

In practice, the Nylas REST API client simplifies this down to two steps.

## Auth

**index.php**

```php
$client = new Nylas(CLIENT, SECRET);

$redirect_url = 'http://localhost:8080/login_callback.php';

$get_auth_url = $client->createAuthURL($redirect_url);

// redirect to Nylas auth server
header("Location: ".$get_auth_url);
```

**login_callback.php**

```php
$access_code = $_GET['code'];

$client = new Nylas(CLIENT, SECRET);

$get_token = $client->getAuthToken($access_code);

// save token in session
$_SESSION['access_token'] = $get_token;
```


## Fetching Threads

```php
// init client!
$client = new Nylas(CLIENT, SECRET, TOKEN);

// Fetch the first thread
$first_thread = $client->threads()->first();

echo $first_thread->id;

// Fetch first 2 latest threads
$two_threads = $client->threads()->all(2);

foreach($two_threads as $thread)
{
    echo $thread->id;
}

// List all threads with 'ben@nylas.com'
$search_criteria = array("any_email" => "ben@nylas.com");

$get_threads = $client->threads()->where($search_criteria)->items()

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

// Mark as Read
$thread->markAsRead();

// Mark as Seen
$thread->markAsSeen();

// Archive
$thread->archive();

// Unarchive
$thread->unarchive();

// Trash
$thread->trash();

// Star
$thread->star();

// Unstar
$thread->unstar();

// Add or remove arbitrary tags
$to_add = array('cfa1233ef123acd12');
$to_remove = array('inbox');

$thread->addTags($to_add);
$thread->removeTags($to_remove);

// Listing messages
foreach($thread->messages()->items() as $message)
{
    echo $message->subject;
    echo $message->body;
}
```

## Working with Files


```php
$client = new Nylas(CLIENT, SECRET, TOKEN);

$file_path = '/var/my/folder/test_file.pdf';

$upload_resp = $client->files()->create($file_path);

echo $upload_resp->id;
```

## Working with Drafts

```php
$client = new Nylas(CLIENT, SECRET, TOKEN);

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

$draft = $client->drafts()->create($message_obj);

$send_message = $draft->send( ['id' => $draft->id] );

echo $send_message->id;
```

## Working with Send Directly

Carefully, send directly is diffrent from create a draft and then send it above.

```php
$client = new Nylas(CLIENT, SECRET, TOKEN);

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

$draft = $client->drafts()->send($message_obj);
```

## Working with Events

```php
$client = new Nylas(CLIENT, SECRET, TOKEN);

$calendars = $client->calendars()->all();

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
$event = $client->events()->create($calendar_data);

echo $event->id;

// update
$event = $event->update(array("location" => "Meeting room #1"));

// delete event
$event->delete();

// delete event (alternate)
$remove = $client->events()->find($event->id)->delete();
```


## End

This nylas-php project is forked by https://github.com/lanlin,

it's not the official version. The official version has many bugs.

And not suport guzzle >6.0 yet. So i fork it, and made many modification.

Surely there's bugs in my fork, but i not have much time on this.

So, help fix these bugs, request pulls are welcome.

