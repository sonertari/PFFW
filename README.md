# PFFW

PFFW is a pf firewall running on OpenBSD. PFFW is expected to be used on production systems. The PFFW project provides a Web UI, an Android application ([A4PFFW](https://github.com/sonertari/A4PFFW)), and a Windows application ([W4PFFW](https://github.com/sonertari/W4PFFW)) for monitoring.

You can find a couple of screenshots on the [PFFW](https://github.com/sonertari/PFFW/wiki), [A4PFFW](https://github.com/sonertari/A4PFFW/wiki), and [W4PFFW](https://github.com/sonertari/W4PFFW/wiki) wikis.

The installation iso file for the amd64 arch is available for download at [pffw62\_20171205\_amd64.iso](https://drive.google.com/file/d/1Z8ucRXQFsAoFtFLffNp_9xC5-Q2rBgQ9/view?usp=sharing). Make sure the SHA256 checksum is correct: 6f113722af0ea5406d907d5f80fbb3fcd28c795716568f8e2d38ab749c559e53.

## Features

PFFW includes the following software, alongside what is already available on a basic OpenBSD installation:

- PFRE: Packet Filter Rule Editor
- PHP
- ISC DNS server
- Symon system monitoring software

![Console](https://github.com/sonertari/PFFW/blob/master/screenshots/Console.png)

The web user interface of PFFW helps you manage your firewall:

- System, network, and service configuration can be achieved on the web interface.
- Pf rules are maintained using PFRE.
- Information on hosts, interfaces, pf rules, states, and queues are provided in a tabular form.
- Pf logs can be viewed and downloaded on the web interface. Compressed log files are supported too.
- Pf statistics collected over logs are displayed in bar charts and top lists. Statistics over compressed log files are supported too.
- You can monitor interfaces, packet transfer, pf states, and kernel memory management on the graphs.
- The web interface supports English and Turkish.

![Dashboard](https://github.com/sonertari/PFFW/blob/master/screenshots/Dashboard.png)

## How to install

Download the installation iso file mentioned above and follow the instructions in the installation guide available in the iso file. Below are the same instructions.

PFFW installation is very intuitive and easy, just follow the instructions on the screen and answer the questions asked. You are advised to accept the default answers to all the questions. In fact, the installation can be completed by accepting default answers all the way from the first question until the last. The only obvious exceptions are network configuration and password setup.

Auto allocator will provide a partition layout recommended for your disk. Suggested partitioning should be suitable for most installations, simply accept it.

Make sure you configure two network interfaces. You will be asked to choose internal and external interfaces later on.

All of the install sets and software packages are selected by default, simply accept the selections.

If the installation script finds an already existing file which needs to be updated, it saves the old file as filename.orig.

Installation logs can be found under the /root directory.

You can access the web administration interface using the IP address of the system's internal interface you have selected during installation. You can log in to the system over ssh from internal network.

Web interface user names are admin and user. Both are set to the same password you provide during installation.

References:

1. INSTALL.amd64 under /6.2/amd64/ in the installation iso file.
2. [Supported hardware](https://www.openbsd.org/amd64.html).
3. [OpenBSD installation guide](https://www.openbsd.org/faq/faq4.html).

## How to build

The purpose in this section is to build the installation iso file using the createiso script at the root of the project source tree. You are expected to be doing these on an OpenBSD 6.2 and have installed git and gettext on it.

The createiso script:

- Clones the git repo of the project to a tmp folder.
- Generates gettext translations and doxygen documentation.
- Prepares the webif and config packages and the site install set.
- And finally creates the iso file.

However, the source tree has links to OpenBSD install sets and packages, which should be broken, hence need to be fixed when you first obtain the sources. Make sure you see those broken links now. So, before you can run createiso, you need to do a couple of things:

- Install sets:
	+ Obtain the sources of OpenBSD.
	+ Copy the files under `openbsd/pffw` to the OpenBSD sources to replace the original files. You are advised to compare the original files with the PFFW versions before replacing.
	+ Build an OpenBSD release, as described in [release(8)](https://man.openbsd.org/release) or [faq5](https://www.openbsd.org/faq/faq5.html).
	+ Copy the required install sets to the appropriate locations to fix the broken links in the sources.
- Packages:
	+ Download the required packages available on the OpenBSD mirrors.
	+ Copy them to the appropriate locations to fix the broken links in the sources.

Note that you can strip down xbase and xfont install sets to reduce the size of the iso file. Copy or link them to the appropriate locations under `openbsd/pffw`.

Now you can run the createiso script which should produce an iso file in the same folder as itself.
