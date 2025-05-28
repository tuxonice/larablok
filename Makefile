# Include this makefile in your Makefile
# And add help text after each target name starting with '\#\#'
# A category can be added with @category
HELP_FUN = \
	%help; \
	while(<>) { push @{$$help{$$2 // 'options'}}, [$$1, $$3] if /^([a-zA-Z\-]+)\s*:.*\#\#(?:@([a-zA-Z\-]+))?\s(.*)$$/ }; \
	print "usage: make [target]\n\n"; \
	for (sort keys %help) { \
	print "${WHITE}$$_:${RESET}\n"; \
	for (@{$$help{$$_}}) { \
	$$sep = " " x (32 - length $$_->[0]); \
	print "  ${YELLOW}$$_->[0]${RESET}$$sep${GREEN}$$_->[1]${RESET}\n"; \
	}; \
	print "\n"; }

help: ##@help show this help
	@perl -e '$(HELP_FUN)' $(MAKEFILE_LIST)
.PHONY: help

start: ##@setup start the application server
	vendor/bin/sail up -d
.PHONY: start

stop: ##@setup stop the application servers
	vendor/bin/sail stop
.PHONY: stop

ssl: ##@setup start the application server using https on port 8443
	vendor/bin/sail up -d
	local-ssl-proxy --source 8443 --target 80 --cert docker/ssl/localhost.pem --key docker/ssl/localhost-key.pem
.PHONY: ssl
