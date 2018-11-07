# PFFW

PFFW is a pf firewall running on OpenBSD. PFFW is expected to be used on production systems. The PFFW project provides a Web User Interface (WUI) for monitoring and configuration. You can also use [A4PFFW](https://github.com/sonertari/A4PFFW) and [W4PFFW](https://github.com/sonertari/W4PFFW) for monitoring.

You can find a couple of screenshots on the [PFFW](https://github.com/sonertari/PFFW/wiki), [A4PFFW](https://github.com/sonertari/A4PFFW/wiki), and [W4PFFW](https://github.com/sonertari/W4PFFW/wiki) wikis.

The installation iso file for the amd64 arch is available for download at [pffw64\_20181107\_amd64.iso](https://drive.google.com/file/d/1Fe2J6XWLSxvye9ks8knmaHIQTWYvoN0K/view?usp=sharing). Make sure the SHA256 checksum is correct: 5799f2c5da17dfb09cf2b581643873583191446036806db2ae3083b9e049a121.

## Features

PFFW includes the following software, alongside what is already available on a basic OpenBSD installation:

- PFRE: Packet Filter Rule Editor
- Symon system monitoring software
- ISC DNS server
- PHP

![Console](https://github.com/sonertari/PFFW/blob/master/screenshots/Console.png)

The web user interface of PFFW helps you manage your firewall:

- Dashboard provides an overview of system status. If enabled, Notifier sends the system status as Firebase push notifications to the Android application, [A4PFFW](https://github.com/sonertari/A4PFFW).
- System, network, and service configuration can be achieved on the web interface.
- Pf rules are maintained using PFRE.
- Information on hosts, interfaces, pf rules, states, and queues are provided in a tabular form.
- System, pf, and network can be monitored via graphs.
- Logs can be viewed and downloaded on the web interface. Compressed log files are supported.
- Statistics collected over logs are displayed in bar charts and top lists. Bar charts and top lists are clickable, so you don't need to touch your keyboard to search anything on the statistics pages. You can view the top lists on pie charts too. Statistics over compressed log files are supported.
- The web interface provides many help boxes and windows, which can be disabled.
- Man pages of OpenBSD and installed software can be accessed and searched on the web interface.
- There are two users who can log in to the web interface. Unprivileged user does not have access rights to configuration pages, thus cannot interfere with system settings, and cannot even change user password (i.e. you can safely give the unprivileged user's password to your boss).
- The web interface supports English and Turkish.
- The web interface configuration pages are designed such that changes you may have made to the configuration files on the command line (such as comments you might have added) remain intact after you configure a module using the web interface.

![Dashboard](https://github.com/sonertari/PFFW/blob/master/screenshots/Dashboard.png)

## How to install

Download the installation iso file mentioned above and follow the instructions in the installation guide available in the iso file. Below are the same instructions.

A few notes about PFFW installation:

- Thanks to a modified auto-partitioner of OpenBSD, the disk can be partitioned with a recommended layout for PFFW, so most users don't need to use the label editor at all.
- All install sets including siteXY.tgz are selected by default, so you cannot 'not' install PFFW by mistake.
- OpenBSD installation questions are modified according to the needs of PFFW. For example, X11 related questions are never asked.
- 512MB RAM and an 8GB HD should be enough.

PFFW installation is very intuitive and easy, just follow the instructions on the screen and answer the questions asked. You are advised to accept the default answers to all the questions. In fact, the installation can be completed by accepting default answers all the way from the first question until the last. The only obvious exceptions are network configuration and password setup.

Auto allocator will provide a partition layout recommended for your disk. Suggested partitioning should be suitable for most installations, simply accept it.

Make sure you configure two network interfaces. You will be asked to choose internal and external interfaces later on.

All of the install sets and software packages are selected by default, simply accept the selections.

If the installation script finds an already existing file which needs to be updated, it saves the old file as filename.orig.

Installation logs can be found under the /root directory.

You can access the web administration interface using the IP address of the system's internal interface you have selected during installation. You can log in to the system over ssh from internal network.

Web interface user names are admin and user. Both are set to the same password you provide during installation.

References:

1. INSTALL.amd64 in the installation iso file.
2. [Supported hardware](https://www.openbsd.org/amd64.html).
3. [OpenBSD installation guide](https://www.openbsd.org/faq/faq4.html).

## How to build

The purpose in this section is to build the installation iso file using the createiso script at the root of the project source tree. You are expected to be doing these on an OpenBSD 6.4 and have installed git, gettext, and doxygen on it.

The createiso script:

- Clones the git repo of the project to a tmp folder.
- Generates gettext translations and doxygen documentation.
- Prepares the webif and config packages and the site install set.
- And finally creates the iso file.

However, the source tree has links to OpenBSD install sets and packages, which should be broken, hence need to be fixed when you first obtain the sources. Make sure you see those broken links now. So, before you can run createiso, you need to do a couple of things:

- Install sets:
	+ Obtain the sources of OpenBSD.
	+ Patch the OpenBSD sources using the `patch-*` files under `openbsd/utmfw`.
	+ Create the UTMFW secret and public key pair to sign and verify the SHA256 checksums of the install sets, and copy them to their appropriate locations. The installation iso of PFFW uses the same install sets as UTMFW, hence the same secret key. If you want to use a different key pair, you should change the references to the UTMFW key pair in the source code as well.
	+ Build an OpenBSD release, as described in [release(8)](https://man.openbsd.org/release) or [faq5](https://www.openbsd.org/faq/faq5.html).
	+ Copy the required install sets to the appropriate locations to fix the broken links in the sources.
- Packages:
	+ Download the required packages available on the OpenBSD mirrors.
	+ Copy them to the appropriate locations to fix the broken links in the sources.

Note that you can strip down xbase and xfont install sets to reduce the size of the iso file. Copy or link them to the appropriate locations under `openbsd/pffw`.

Now you can run the createiso script which should produce an iso file in the same folder as itself.
