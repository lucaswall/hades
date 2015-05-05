#|/bin/bash
cd "`dirname $0`"
export BRANCH="develop"
./ci_jenkins_build.sh
