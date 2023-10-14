#!/usr/bin/env bash

set -e
set -x

CURRENT_BRANCH="main"

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

remote auth git@github.com:quantaforge/auth.git
remote broadcasting git@github.com:quantaforge/broadcasting.git
remote bus git@github.com:quantaforge/bus.git
remote cache git@github.com:quantaforge/cache.git
remote collections git@github.com:quantaforge/collections.git
remote conditionable git@github.com:quantaforge/conditionable.git
remote config git@github.com:quantaforge/config.git
remote console git@github.com:quantaforge/console.git
remote container git@github.com:quantaforge/container.git
remote contracts git@github.com:quantaforge/contracts.git
remote cookie git@github.com:quantaforge/cookie.git
remote database git@github.com:quantaforge/database.git
remote encryption git@github.com:quantaforge/encryption.git
remote events git@github.com:quantaforge/events.git
remote filesystem git@github.com:quantaforge/filesystem.git
remote hashing git@github.com:quantaforge/hashing.git
remote http git@github.com:quantaforge/http.git
remote log git@github.com:quantaforge/log.git
remote macroable git@github.com:quantaforge/macroable.git
remote mail git@github.com:quantaforge/mail.git
remote notifications git@github.com:quantaforge/notifications.git
remote pagination git@github.com:quantaforge/pagination.git
remote pipeline git@github.com:quantaforge/pipeline.git
remote process git@github.com:quantaforge/process.git
remote queue git@github.com:quantaforge/queue.git
remote redis git@github.com:quantaforge/redis.git
remote routing git@github.com:quantaforge/routing.git
remote session git@github.com:quantaforge/session.git
remote support git@github.com:quantaforge/support.git
remote testing git@github.com:quantaforge/testing.git
remote translation git@github.com:quantaforge/translation.git
remote validation git@github.com:quantaforge/validation.git
remote view git@github.com:quantaforge/view.git

split 'src/QuantaForge/Auth' auth
split 'src/QuantaForge/Broadcasting' broadcasting
split 'src/QuantaForge/Bus' bus
split 'src/QuantaForge/Cache' cache
split 'src/QuantaForge/Collections' collections
split 'src/QuantaForge/Conditionable' conditionable
split 'src/QuantaForge/Config' config
split 'src/QuantaForge/Console' console
split 'src/QuantaForge/Container' container
split 'src/QuantaForge/Contracts' contracts
split 'src/QuantaForge/Cookie' cookie
split 'src/QuantaForge/Database' database
split 'src/QuantaForge/Encryption' encryption
split 'src/QuantaForge/Events' events
split 'src/QuantaForge/Filesystem' filesystem
split 'src/QuantaForge/Hashing' hashing
split 'src/QuantaForge/Http' http
split 'src/QuantaForge/Log' log
split 'src/QuantaForge/Macroable' macroable
split 'src/QuantaForge/Mail' mail
split 'src/QuantaForge/Notifications' notifications
split 'src/QuantaForge/Pagination' pagination
split 'src/QuantaForge/Pipeline' pipeline
split 'src/QuantaForge/Process' process
split 'src/QuantaForge/Queue' queue
split 'src/QuantaForge/Redis' redis
split 'src/QuantaForge/Routing' routing
split 'src/QuantaForge/Session' session
split 'src/QuantaForge/Support' support
split 'src/QuantaForge/Testing' testing
split 'src/QuantaForge/Translation' translation
split 'src/QuantaForge/Validation' validation
split 'src/QuantaForge/View' view
