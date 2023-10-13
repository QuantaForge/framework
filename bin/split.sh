#!/usr/bin/env bash

set -e
set -x

CURRENT_BRANCH="10.x"

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

remote auth git@github.com:quantaquirk/auth.git
remote broadcasting git@github.com:quantaquirk/broadcasting.git
remote bus git@github.com:quantaquirk/bus.git
remote cache git@github.com:quantaquirk/cache.git
remote collections git@github.com:quantaquirk/collections.git
remote conditionable git@github.com:quantaquirk/conditionable.git
remote config git@github.com:quantaquirk/config.git
remote console git@github.com:quantaquirk/console.git
remote container git@github.com:quantaquirk/container.git
remote contracts git@github.com:quantaquirk/contracts.git
remote cookie git@github.com:quantaquirk/cookie.git
remote database git@github.com:quantaquirk/database.git
remote encryption git@github.com:quantaquirk/encryption.git
remote events git@github.com:quantaquirk/events.git
remote filesystem git@github.com:quantaquirk/filesystem.git
remote hashing git@github.com:quantaquirk/hashing.git
remote http git@github.com:quantaquirk/http.git
remote log git@github.com:quantaquirk/log.git
remote macroable git@github.com:quantaquirk/macroable.git
remote mail git@github.com:quantaquirk/mail.git
remote notifications git@github.com:quantaquirk/notifications.git
remote pagination git@github.com:quantaquirk/pagination.git
remote pipeline git@github.com:quantaquirk/pipeline.git
remote process git@github.com:quantaquirk/process.git
remote queue git@github.com:quantaquirk/queue.git
remote redis git@github.com:quantaquirk/redis.git
remote routing git@github.com:quantaquirk/routing.git
remote session git@github.com:quantaquirk/session.git
remote support git@github.com:quantaquirk/support.git
remote testing git@github.com:quantaquirk/testing.git
remote translation git@github.com:quantaquirk/translation.git
remote validation git@github.com:quantaquirk/validation.git
remote view git@github.com:quantaquirk/view.git

split 'src/QuantaQuirk/Auth' auth
split 'src/QuantaQuirk/Broadcasting' broadcasting
split 'src/QuantaQuirk/Bus' bus
split 'src/QuantaQuirk/Cache' cache
split 'src/QuantaQuirk/Collections' collections
split 'src/QuantaQuirk/Conditionable' conditionable
split 'src/QuantaQuirk/Config' config
split 'src/QuantaQuirk/Console' console
split 'src/QuantaQuirk/Container' container
split 'src/QuantaQuirk/Contracts' contracts
split 'src/QuantaQuirk/Cookie' cookie
split 'src/QuantaQuirk/Database' database
split 'src/QuantaQuirk/Encryption' encryption
split 'src/QuantaQuirk/Events' events
split 'src/QuantaQuirk/Filesystem' filesystem
split 'src/QuantaQuirk/Hashing' hashing
split 'src/QuantaQuirk/Http' http
split 'src/QuantaQuirk/Log' log
split 'src/QuantaQuirk/Macroable' macroable
split 'src/QuantaQuirk/Mail' mail
split 'src/QuantaQuirk/Notifications' notifications
split 'src/QuantaQuirk/Pagination' pagination
split 'src/QuantaQuirk/Pipeline' pipeline
split 'src/QuantaQuirk/Process' process
split 'src/QuantaQuirk/Queue' queue
split 'src/QuantaQuirk/Redis' redis
split 'src/QuantaQuirk/Routing' routing
split 'src/QuantaQuirk/Session' session
split 'src/QuantaQuirk/Support' support
split 'src/QuantaQuirk/Testing' testing
split 'src/QuantaQuirk/Translation' translation
split 'src/QuantaQuirk/Validation' validation
split 'src/QuantaQuirk/View' view
