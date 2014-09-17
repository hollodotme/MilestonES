# Event payload and meta data

Every event object __must have__ a set of data that will be applied to the event sources object. This is the payload of an event.

Every event object __could have__ a set of data that will describe the source of the event, its reason, the user who triggered the event and so on.
This is the meta data of an event.

## Conventions

* Both, payload and meta data sets __must__ be of serializable structure to be stored as a single string to the event store.

* The structure should be as plain as possible to minify the complexity of serialization which is done by the implementations of `Interfaces\SerializesData`.

* If possible use `\stdClass` objects or `array`s.


