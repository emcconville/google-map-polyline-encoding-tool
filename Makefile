SRC_DIR = src
TEST_DIR = tests
PHPUNIT ?= `which phpunit 2>/dev/null`

PREFIX = .
DIST_DIR = ${PREFIX}/dist
PLY = Polyline.php

GMPET = ${DIST_DIR}/${PLY} 
SRC_GMPET = ${SRC_DIR}/${PLY} 

GMPET_VER = $(shell git log -1 --pretty=format:%h\ %p\ %t)
GMPET_DATE = $(shell git log -1 --date=short --pretty=format:%ad)

${DIST_DIR}:
	@@mkdir -p ${DIST_DIR}

all: polyline test goodbye

clean:
	@@echo "Removing polyline build: " ${DIST_DIR}
	@@rm -rf ${DIST_DIR}

goodbye: 
	@@echo "Build complete"

polyline: ${SRC_GMPET} | ${DIST_DIR}
	@@echo "Building Polyline"
	
	@@cat ${SRC_GMPET} | \
		sed 's/@VERSION@/'"${GMPET_VER}"'/' | \
		sed 's/@DATE@/'"${GMPET_DATE}"'/' > ${GMPET};
		
test: 
	@@if test ! -z ${PHPUNIT}; then \
		echo "Testing build"; \
		${PHPUNIT} --tap ; \
	else \
		echo "PHPUnit not installed. Skipping build test."; \
	fi
		
.PHONY: all clean goodbye polyline test
 