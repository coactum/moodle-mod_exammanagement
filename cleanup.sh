#!/bin/bash

vagrant halt
vagrant destroy -f

rm -Rf data33
rm -Rf data34

mkdir data33
mkdir data34