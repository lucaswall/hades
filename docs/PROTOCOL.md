
# Hades Mobile Backend Protocol

## General

All server functionality is accessed through different REST endpoints on the production and the development server.

Production server: http://mobileapi.qb9.net

Development server: http://mobileapi-test.qb9.net

POST methods must send data using JSON and specify "Content-Type: application/json" in the HTTP header accordingly.

Calls that requite security must calculate an HMAC-SHA1 signature for the whole JSON data and send it with an "X-Security: hmac-sha1 *signature*" header.

Server has two distinct keys for the HMAC calculation, one for client application and another one for game server.

Sample header:

```
POST /v1/push/send HTTP/1.1
Host: mobileapi-test.qb9.net
Accept: */*
Content-Type: application/json
Content-Length: 153
X-Security: hmac-sha1 c0746532ed6d1f8fbfb9d7e58f7da94e6b4a35c3
```

All methods return "OK" on the "result" field for success, or an encoded error string on failure.

## Push notifications

### POST /v1/push/register

This method must be signed with the **client key**.

| field        | type   | description                                      |
| ------------ | ------ | ------------------------------------------------ |
| gameid       | string | Id of the game registering the device            |
| userid       | string | Id of the user registering the device            |
| device_token | string | Token provided by Apple APN or Google GCM server |
| ostype       | string | 'ios' or 'android'                               |

Register a device for push notification. The client app must previously get the device token from APN or GCM service. For Apple APN the device token must be base64 encoded.

The userid field is a free string used by the game server to identify the user.

### POST /v1/push/unregister

This method must be signed with the **client key**.

| field  | type   | description                        |
| ------ | ------ | ---------------------------------- |
| gameid | string | Id of the game deleting the device |
| userid | string | Id of the user deleting the device |

Eliminate a device token for this user.

### POST /v1/push/send

This method must be signed with the **server key**.

| field  | type   | description                           |
| ------ | ------ | ------------------------------------- |
| gameid | string | Id of the game sending a push message |
| userid | string | Id of the user this message is for    |
| data   | object | Message payload                       |

Sends a message to all devices registered for user userid.

Data object can include the following fields:

| field | type   | description                                    |
| ----- | ------ | ---------------------------------------------- |
| title | string | Notification title                             |
| text  | string | Notification body                              |
| badge | int    | Badge to set on application icon               |
| sound | string | Sound to play for notification                 |
| extra | object | Key/value extra params to send in notification |

Sample json data:

```
{
    "gameid": "net.qb9.notifsample",
    "userid": "kthulhu",
    "data": {
        "title": "Hello!",
        "text": "Look this now!",
        "badge": 76,
        "sound": "default",
        "extra": {
            "some_id": 76
        }
    }
}
```

Different payloads can be included for Android and iOS. If field "for_ios" or "for_android", both object type, are defined they will override the whole payload for that OS.

Note that Android data object is dependent on sample since Android app must generate the whole notification with code. 

See Apple APN payload reference at http://goo.gl/tw94ad.
