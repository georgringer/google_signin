# TYPO3 Extension `google_signin`

This extension makes it possible to sign in in your TYPO3 websites by your google account. Less passwords, faster logins

## Requirement

- TYPO3 CMS 8+
- https
- Basic knowledge about Google API configuration

## Installation & Configuration

## Create an account on Google Cloud Platform

1st) Go to https://console.cloud.google.com/ and create a new project.

### create credentials

switch to **credentials** and click button **create credentials** and choose *oauth client ID*

step-create-credentials.png

- application type: web application
- authorized javascript origins: add domain(s)

step-create-client.png

=> client id

step-create-client-created.png

### authorize ownership

go to https://www.google.com/webmasters/tools/home?hl=EN and add site by clicking button **add property**

add domain.

only the recommended method *HTML file upload* is supported. follow the instructions and press button **verify**.

## install extension

install extension as usual.

### configuration

in em:

- add client id
- enable be

# todos

- check ssl
- stabilize service
    - logs
    - validate in own package
- fe auth
- error in js
- docs
- testing
- auto create user for business