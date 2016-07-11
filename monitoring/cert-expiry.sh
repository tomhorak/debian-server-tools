#!/bin/bash
#
# Check certificate expiry.
#
# VERSION       :0.5.1
# DATE          :2016-06-19
# AUTHOR        :Viktor Szépe <viktor@szepe.net>
# LICENSE       :The MIT License (MIT)
# URL           :https://github.com/szepeviktor/debian-server-tools
# BASH-VERSION  :4.2+
# DEPENDS       :apt-get install openssl ca-certificates
# LOCATION      :/usr/local/sbin/cert-expiry.sh
# CRON-WEEKLY   :/usr/local/sbin/cert-expiry.sh
# CONFIG        :/etc/certexpiry

# @TODO Add support for starttls: HOST:PORT:smtp HOST:PORT:imap

# Alert 10 days before expiration
ALERT_DAYS="10"
CERT_EXPIRY_CONFIG="/etc/certexpiry"

Check_cert() {
    local CERT="$1"
    local -i END_SEC="$((ALERT_DAYS * 86400))"
    local CERT_SUBJECT

    # Not a base64 encoded certificate
    if ! grep -q -- "-BEGIN CERTIFICATE-" "$CERT"; then
        return 1
    fi

    # Alert
    if [ "$(openssl x509 -in "$CERT" -checkend "$END_SEC")" != "Certificate will not expire" ]; then
        CERT_SUBJECT="$(openssl x509 -in "$CERT" -noout -subject | cut -d "=" -f 2-)"
        EXPIRY_DATE="$(openssl x509 -in "$CERT" -noout -enddate | cut -d "=" -f 2-)"
        echo "${CERT_SUBJECT} (${CERT}) expires at ${EXPIRY_DATE}"
        return 3
    fi
}

# Certificates in /etc/ excluding /etc/ssl/certs/ and /etc/letsencrypt/archive/
find /etc/ -not -path "/etc/ssl/certs/*" -not -path "/etc/letsencrypt/archive/*" \
    "(" -iname "*.crt" -or -iname "*.pem" ")" \
    | while read -r CERT; do
        Check_cert "$CERT"
    done

# Remote certificates
if [ -r "$CERT_EXPIRY_CONFIG" ]; then
    # CERT_EXPIRY_REMOTES=( host:port )
    source "$CERT_EXPIRY_CONFIG"
fi
if [ -n "${CERT_EXPIRY_REMOTES[*]}" ]; then
    for HOST_PORT in "${CERT_EXPIRY_REMOTES[@]}"; do
        # Set file name for expiry reporting
        CERT_EXPIRY_TMP="$(mktemp "/tmp/${HOST_PORT%%:*}-XXXXXXXXXX")"
        openssl s_client -CAfile /etc/ssl/certs/ca-certificates.crt \
            -connect "$HOST_PORT" -servername "${HOST_PORT%%:*}" \
            < /dev/null 1> "$CERT_EXPIRY_TMP" 2> /dev/null
        Check_cert "$CERT_EXPIRY_TMP"
        rm -f "$CERT_EXPIRY_TMP"
    done
fi

exit 0
