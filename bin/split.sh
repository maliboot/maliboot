#!/usr/bin/env bash

set -e
set -x

# 在目录下运行 ./bin/split.sh (无commit的情况下，无需运行)

CURRENT_BRANCH="master"
BASEPATH=$(cd `dirname $0`; cd ../; pwd)
REPOS=$@
REPOS_ONLY=(
api-annotation
auth
cola
contract
di
dto
error-code
exception-handler
field-collector
hashing
passport
plugin
plugin-code-generator
plugin-config
request
response
response-wrapper
swagger
utils
validation
)

function split()
{
    SHA1=`$BASEPATH/bin/splitsh-lite --prefix=$1`
    git push $2 "$SHA1:refs/heads/$CURRENT_BRANCH" -f
}

function remote()
{
    git remote add $1 $2 || true
}

git pull origin $CURRENT_BRANCH
#
if [[ $# -eq 0 ]]; then
    REPOS=$(ls $BASEPATH/src)
fi
#
for REPO in $REPOS ; do
    if [[ ${REPOS_ONLY[@]/${REPO}/} = ${REPOS_ONLY[@]} ]]; then
        continue ;
    fi
    remote $REPO git@github.com:maliboot/$REPO.git
    split "src/$REPO" $REPO
done