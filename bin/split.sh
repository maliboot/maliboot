#!/usr/bin/env bash

set -e
set -x

# 在目录下运行 ./bin/split.sh (无commit的情况下，无需运行)

CURRENT_BRANCH="master"
BASEPATH=$(cd `dirname $0`; cd ../src; pwd)
REPOS=$@

function split()
{
    SHA1=`./bin/splitsh-lite --prefix=$1`
    git push $2 "$SHA1:refs/heads/$CURRENT_BRANCH" -f
}

function remote()
{
    git remote add $1 $2 || true
}

git pull origin $CURRENT_BRANCH
#
if [[ $# -eq 0 ]]; then
    REPOS=$(ls $BASEPATH)
fi
#
for REPO in $REPOS ; do
    remote $REPO git@github.com:maliboot/$REPO.git
    split "src/$REPO" $REPO
done