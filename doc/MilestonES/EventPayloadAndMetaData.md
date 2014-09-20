# Event payload_dto and meta data

Every event_envelope object __must have__ a set of data that will be applied to the event_envelope sources object. This is the payload_dto of an event_envelope.

Every event_envelope object __could have__ a set of data that will describe the source of the event_envelope, its reason, the user who triggered the event_envelope and so on.
This is the meta data of an event_envelope.

## Conventions

* Both, payload_dto and meta data sets __must__ be of serializable structure to be stored as a single string to the event_envelope store.

* The structure should be as plain as possible to minify the complexity of serialization which is done by the implementations of `Interfaces\SerializesData`.

* If possible use `\stdClass` objects or `array`s.


