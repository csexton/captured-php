OPTIONS=-e ssh --relative --compress --archive --progress --human-readable

# Make sure to set CAPTURED_DEPLOY_SERVER to the server you want to rsync to.
# The syntax might look like this:
#
# export CAPTURED_DEPLOY_SERVER="username@hostname.com:path/to/php/dir/"
#
SERVER=$(CAPTURED_DEPLOY_SERVER)

all: deploy
deploy:
	rsync $(OPTIONS) ./ $(SERVER) --exclude="Makefile" --exclude="*DS_Store" --exclude=".git"

