# EventStreamIdentifier

* implements `Interfaces\IdentifiesEventStream`
* final

## Abstract

An object of type EventStreamIdentifier is used to retrieve a unique event_envelope stream from the event_envelope store.

## Convention

An event_envelope stream is identified by an ID and a type ID.
The type ID is used to guard uniqueness of IDs within the scope of the type, but without uniqueness in the whole system.

Every ID has to be a unique class implementing `Interfaces\Identifies`.
The type ID is build from the ID's class name and also implements `Interfaces\Identifies`.

## Example

```php

namespace My\Namespace;

// Create a UserId class that represents a UUID
class UserId extends hollodotme\MilestonES\UniversalUniqueIdentifier
{

}

// Create a universal unique user id
$user_id = UserId::generate();

$event_stream_id = new EventStreamIdentifier($user_id);

echo 'Stream-ID: ' . $event_stream_id->getStreamId();
echo 'Stream-Type: ' . $event_stream_id->getStreamIdContract();
```

This example will output something like this:

```
Stream-ID: bfa3c279-65d2-4f51-a76b-a9f06c6ac467
Steam-Type: My.Namespace.UserId
```
