# Slim PHP with Swoole - Boilerplate Project

#### Light-weight boilerplate for simple projects that don't want the bloat - Designed to promote pure php containers that don't need nginx or apache.

###

##### *In order to run locally, you will first need to install Swoole: https://www.swoole.co.uk/docs/get-started/installation

Then, you can run the project locally by running the following command from project root:

`php public/index.php`

Then to go 'localhost:8888' in your browser. (Unless you update the port in the instantiation of the Swoole\Http\Server class)

Or, you can run the project locally inside of docker container by first building the image (also from project root):

`docker build .`

This should return an image hash when it completes.  You take that returned value (we will refer to it as ${IMAGE_HASH}) and run the following command:

`docker run -d -p 8888:8888 ${IMAGE_HASH} php /src/public/index.php`

You can remove the -d if you don't want the container to run in detached mode.

You can also change the ports that are forwarding into the container if you are already using port 8888.
