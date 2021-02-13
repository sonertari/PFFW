# !/bin/sh
# Copyright (C) 2020, 2021 Soner Tari <sonertari@gmail.com>

# TODO: Handle errors

if [ -z "$PREFIX" ]; then
	PREFIX=/etc
fi
echo "PREFIX=$PREFIX"

if [ -z "$SET_SERIAL" ]; then
	SET_SERIAL=1
fi
echo "SET_SERIAL=$SET_SERIAL"

install_file() {
	local _filename=$1 _src=$2 _dst=$3 _mod=$4 _own=$5

    _filepath="$_dst/$_filename"
    if [ -f $_filepath ]; then
        cp $_filepath $_filepath.orig && echo "Saved old file as $_filepath.orig"
    fi
    cp "$_src/$_filename" $_filepath
    chmod $_mod $_filepath
    chown $_own $_filepath
}

# httpd
cd httpd
openssl genrsa -out ca.key 2048
openssl req -new -nodes -x509 -sha256 -out ca.crt -key ca.key -extensions v3_ca -set_serial $SET_SERIAL -days 365 \
    -config httpd_ca.cnf \
    -subj "/C=TR/ST=Antalya/L=Serik/O=ComixWall/OU=PFFW/CN=example.org/emailAddress=sonertari@gmail.com"

openssl req -new -nodes -sha256 -keyout server.key -out server.csr \
    -config httpd.cnf \
    -subj "/C=TR/ST=Antalya/L=Serik/O=ComixWall/OU=PFFW/CN=example.org/emailAddress=sonertari@gmail.com"
openssl x509 -req -CA ca.crt -CAkey ca.key -in server.csr -out server.crt -extensions server -set_serial $SET_SERIAL -days 365
cd ..

install_file "server.crt" "httpd" "$PREFIX/ssl" "644" "root:bin"
install_file "server.key" "httpd" "$PREFIX/ssl/private" "644" "root:bin"

