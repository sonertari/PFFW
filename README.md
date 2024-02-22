# PFFW

PFFW is a pf firewall running on OpenBSD. PFFW is expected to be used on production systems. The PFFW project provides a Web User Interface (WUI) for monitoring and configuration. You can also use the Android application [A4PFFW](https://github.com/sonertari/A4PFFW), which can display the notifications sent from PFFW, and the Windows application [W4PFFW](https://github.com/sonertari/W4PFFW) for monitoring.

PFFW is the stripped-down version of [UTMFW](https://github.com/sonertari/UTMFW) without the UTM features.

![Dashboard](https://github.com/sonertari/PFFW/blob/master/screenshots/Dashboard.png)

You can find a couple of screenshots on the [wiki](https://github.com/sonertari/PFFW/wiki).

## Download

The PFFW project releases three installation files:

- The installation iso file for the amd64 arch is available for download at [pffw74\_20240219\_amd64.iso](https://drive.google.com/file/d/187zPER5mZd2lKTVblCQS0HW6CIo7hgIf/view?usp=share_link). Make sure the SHA256 checksum is correct: 93692e1a71d484c53573e6a77f725e320a58954af9257abe3af228bb5d28ad1c.

- The installation img file for the amd64 arch is available for download at [pffw74\_20240219\_amd64.img](https://drive.google.com/file/d/1nck37ZRnbG81N1frv7jA1eoa8madqPE8/view?usp=share_link). Make sure the SHA256 checksum is correct: bb8bb69fce4bb3d0a031c740eba894e8126c0b51d62f566a0f0d036d7348b05a.

- The installation img file for the arm64 arch is available for download at [pffw74\_20240219\_arm64.img](https://drive.google.com/file/d/1JkSLlZ0MvcGNY4QOjcl9t6mMh5Gp3r1Q/view?usp=share_link). Make sure the SHA256 checksum is correct: 37afa742c58edebb01bbb026580c4f436bfe2ada60a8ec9ddb98ee95f757e590. The only arm64 platform supported is Raspberry Pi 4 Model B.

## Features

PFFW includes the following software, alongside what is already available on a basic OpenBSD installation:

- [PFRE](https://github.com/sonertari/PFRE): Packet Filter Rule Editor
- Symon: System monitoring software
- Collectd: System metrics collection engine
- Dnsmasq: DNS forwarder
- PHP

![Console](https://github.com/sonertari/PFFW/blob/master/screenshots/Console.png)

The web user interface of PFFW helps you manage your firewall:

- Dashboard displays an overview of system status using graphs and statistics counters. You can click on those graphs and counters to go to their details on the web user interface.
- Notifier sends the system status as Firebase push notifications to the Android application, [A4PFFW](https://github.com/sonertari/A4PFFW).
- System, network, and service configuration can be achieved on the web user interface.
- Pf rules are maintained using [PFRE](https://github.com/sonertari/PFRE).
- Information on hosts, interfaces, pf rules, states, and queues are provided in tabular form.
- System, pf, and network can be monitored via graphs.
- Logs can be viewed and downloaded on the web user interface. Compressed log files are supported.
- Statistics collected over logs are displayed in bar charts and top lists. Bar charts and top lists are clickable, so you don't need to touch your keyboard to search anything on the statistics pages. You can view the top lists on pie charts too. Statistics over compressed log files are supported.
- The web user interface provides many help boxes and windows, which can be disabled.
- Man pages of OpenBSD and installed software can be accessed and searched on the web user interface.
- There are two users who can log in to the web user interface. Unprivileged user does not have access rights to configuration pages, thus cannot interfere with system settings, and cannot even change user password (i.e. you can safely give the unprivileged user's password to your boss).
- The web user interface supports English and Turkish.
- The web user interface configuration pages are designed such that changes you may have made to the configuration files on the command line (such as comments you might have added) remain intact after you configure a module using the web user interface.

PFFW uses the same design decisions and implementation as the [PFRE](https://github.com/sonertari/PFRE) project. See its README for details.

![UI Design](https://github.com/sonertari/UTMFW/blob/master/screenshots/UIDesign.png)

## How to install

Download the installation iso or img file for your platform and follow the instructions in the installation guide available in the file. Below are the same instructions.

You can also find [the output of a sample installation on the UTMFW wiki](https://github.com/sonertari/UTMFW/wiki/Sample-Installation).

### Installation Guide

PFFW installation is very intuitive and easy, just follow the instructions on the screen and answer the questions asked. You are advised to accept the default answers to all the questions. In fact, the installation can be completed by accepting default answers all the way from the first question until the last. The only exceptions are network configuration, password setup, and installation disk selection.

Auto allocator will provide a partition layout recommended for your disk. Suggested partitioning should be suitable for most installations, simply accept it. Do not delete or modify the msdos partition (for arm64 installation).

Make sure you configure two network interfaces. You will be asked to choose internal and external interfaces later on. You can configure the internal wifi interface in Host AP mode.

All of the install sets and software packages are selected by default, simply accept the selections.

While installing using the img file, when the installation script asks the location for the install sets or the packages, you should choose the disk option and that the disk partition is not mounted yet, and then select the device name for the installation disk (usually sd0 or sd1, but type ? to see device info first). The default path for install sets and packages the script offers is the same path as in the img file too, so you just hit Enter at that point.

If the installation script finds an already existing file which needs to be updated, it saves the old file as filename.orig.

Installation logs can be found under the /root directory.

You can access the web administration interface using the IP address of the system's internal interface you have selected during installation. You can log in to the system over ssh from internal network.

Web interface user names are admin and user. Both are set to the same password you provide during installation.

References:

1. INSTALL.amd64 in the installation iso file and INSTALL.arm64 in the installation img file.
2. [Supported hardware for amd64](https://www.openbsd.org/amd64.html) and [supported hardware for arm64](https://www.openbsd.org/arm64.html).
3. [OpenBSD installation guide](https://www.openbsd.org/faq/faq4.html).

### Installation Tips

A few notes about PFFW installation:

- Thanks to a modified auto-partitioner of OpenBSD, the disk can be partitioned with a recommended layout for PFFW, so most users don't need to use the label editor at all.
- All install sets including siteXY.tgz are selected by default, so you cannot 'not' install PFFW by mistake.
- OpenBSD installation questions are modified according to the needs of PFFW. For example, X11 related questions are never asked.
- 512MB RAM and an 8GB HD should be enough.
- If you install on an SD card, make sure it is fast enough. If you install on a slow disk, but you have enough RAM, you can enable memory-based file system (MFS), which is the default.
- When you first try to log in to the WUI, ignore the certificate warning issued by your web browser and proceed to the WUI.
- Make sure the date and time of the system is correct during both installation and normal operation, and select the correct timezone during installation. Otherwise, after fixing the date and time of the system during normal operation, the system statistics and monitoring programs may stop updating the RRD files due to significant time difference since last update. So you may need to delete the statistics files and reinit the RRD files using the WUI, and restart either the statistics and monitoring programs or the system.

## How to build

The purpose in this section is to build the installation iso or img file using the createiso or createimg script, respectively, at the root of the project source tree. You are expected to be doing these on an OpenBSD 7.4 and have installed git, gettext, and doxygen on it.

### Build summary

The create script:

- Clones the git repo of the project to a tmp folder.
- Generates gettext translations and doxygen documentation.
- Prepares the webif and config packages and the site install set.
- And finally creates the iso file for the amd64 arch or the img file for the arm64 arch.

However, the source tree has links to OpenBSD install sets and packages, which should be broken, hence need to be fixed when you first obtain the sources. Make sure you see those broken links now. So, before you can run the create scripts, you need to do a couple of things:

- Install sets:
	+ Obtain the sources of OpenBSD.
	+ Patch the OpenBSD sources using the `patch-*` files under `openbsd/pffw`.
	+ Create the UTMFW secret and public key pair to sign and verify the SHA256 checksums of the install sets, and copy them to their appropriate locations. The installation iso and img files of PFFW use the same install sets as UTMFW, hence the same secret key. If you want to use a different key pair, you should change the references to the UTMFW key pair in the source code as well.
	+ Build an OpenBSD release, as described in [release(8)](https://man.openbsd.org/release) or [faq5](https://www.openbsd.org/faq/faq5.html).
	+ Copy the required install sets to the appropriate locations to fix the broken links in the sources.
- Packages:
	+ Download the required packages available on the OpenBSD mirrors.
	+ Create the packages which have been modified for PFFW: collectd (see the `ports` folder of UTMFW).
	+ Copy them to the appropriate locations to fix the broken links in the sources.

Note that you can strip down xbase and xfont install sets to reduce the size of the iso and img files. Copy or link them to the appropriate locations under `openbsd/pffw`.

Now you can run the createiso or createimg script, which should produce an iso or img file, respectively, in the same folder as itself.

### Build steps

The following are steps you can follow to build PFFW yourself. Some of these steps can be automated by a script. You can modify these steps to suit your needs.

- Install OpenBSD amd64:
	+ Download installXY.iso from an OpenBSD mirror
	+ Create a new VM with 60GB disk, or choose a size based on your needs
	+ Start the VM and install OpenBSD

- Install OpenBSD arm64:
	+ Download installXY.img from an OpenBSD mirror
	+ Use a 32GB SD card or USB flash memory, or choose a size based on your needs
	+ Start the Raspberry Pi 4 or qemu-system-aarch64 and install OpenBSD

- Configure OpenBSD:
	+ Create a local user, after reboot add it to /etc/doas.conf
	+ Create a separate partition mounted on /dest, which will be needed to make release(8)
	+ Add noperm to /dest in /etc/fstab
	+ Create /dest/dest/ and /dest/rel/ folders
	+ Make /dest owned by build:wobj and set its perms to 700
		```
		doas chown -R build:wobj /dest
		doas chmod -R 700 /dest
		```

- Fetch the PFFW sources and update if upgrading:
	+ Install git
	+ Clone PFFW to your home folder

	+ Bump the version number X.Y in the sources, if upgrading
		+ cd/amd64/etc/boot.conf
		+ cd/arm64/etc/boot.conf
		+ meta/create
		+ meta/install.sub
		+ src/create_po.sh
		+ Doxyfile
		+ README.md
		+ src/lib/defs.php
		+ cd/amd64/X.Y/
		+ cd/arm64/X.Y/
		+ openbsd/X.Y/
		+ .po files under src/View/locale/

	+ Bump the version number XY in the sources, if upgrading
		+ README.md
		+ openbsd/pffw/expat/amd64/xbaseXY.tgz
		+ openbsd/pffw/expat/arm64/xbaseXY.tgz
		+ openbsd/pffw/fonts/amd64/xfontXY.tgz
		+ openbsd/pffw/fonts/arm64/xfontXY.tgz

	+ Update the version number, release date, project changes, and news, if upgrading
		+ config/etc/motd
		+ meta/root.mail
		+ README.md

	+ Update copyright year if necessary

- Make sure the signify key pair for UTMFW is in the correct locations:
	+ Save utmfw-XY.pub and utmfw-XY.sec to docs/signify
	+ Copy utmfw-XY.pub to meta/etc/signify/
	+ Copy utmfw-XY.pub to /etc/signify/, the utmfw-XY.pub file is copied into the bsd.rd file while making release(8), to verify install sets during installation

- Update the packages for the amd64 arch, then do the same for the arm64 arch replacing amd64 with arm64 (or aarch64 for PKG_PATH) below:
	+ Install the OpenBSD packages
		+ Set the download mirror, use the existing cache if any
			```
			export PKG_PATH=/var/db/pkg_cache/:https://cdn.openbsd.org/pub/OpenBSD/X.Y/packages/amd64/
			```
		+ Save the depends under PKG_CACHE, which will be used later on to update the packages in the iso and img files
			```
			export PKG_CACHE=/var/db/pkg_pffw/
			```
		+ dnsmasq
		+ symon
		+ symux
		+ pftop
		+ php, php-cgi, php-curl, php-pcntl

	+ Build and create the PFFW packages
		+ Extract ports.tar.gz under /usr/
		+ Copy the port folder of collectd under ports in UTMFW to /usr/ports/sysutils
		+ Install the pkg depends of collectd before making them, so that the ports system does not try to build and install them itself
		+ Make the collectd packages
		+ Sign the collectd packages using signify, for example:
			```
			signify -Sz -s utmfw-XY.sec -m /usr/ports/packages/amd64/all/collectd-5.12.0p0.tgz -x ~/collectd-5.12.0p0.tgz
			```
	+ Install the PFFW packages using their signed packages, to download their dependencies
		+ Save the depends under PKG_CACHE
			```
			export PKG_CACHE=/var/db/pkg_pffw/
			```
		+ collectd

	+ Update the links under cd/amd64/X.Y/packages/ with the OpenBSD packages saved under PKG_CACHE

	+ Keep the links for
		+ collectd

- Update meta/install.sub:
	+ Update the versions of the packages listed in THESETS

- Make release(8) for the amd64 arch, then do the same for the arm64 arch replacing amd64 with arm64 below:
	+ Extract src.tar.gz and and sys.tar.gz under /usr/src/
	+ Apply the patches under openbsd/pffw
	+ Update the sources with the stable branch changes if any
	+ Follow the instructions in release(8), this step takes about 6 hours on a relatively fast amd64 computer and longer than 60 hours on a Raspberry Pi 4
		+ Build the kernel and reboot
		+ Build the base system
		+ Make the release, use the dest and rel folders created above:
			```
			export DESTDIR=/dest/dest/ RELEASEDIR=/dest/rel/
			```
	+ Copy the install sets under /dest/rel/ to ~/OpenBSD/X.Y/amd64/

- Update the install sets:
	+ Update the links for install sets under cd/amd64/X.Y/amd64 using the install sets under ~/OpenBSD/X.Y/amd64/ made above
	+ Update the links for install sets under cd/arm64/X.Y/arm64 using the install sets under ~/OpenBSD/X.Y/arm64/ made above
	+ Remove the old links
	+ Copy the xbaseXY.tgz install set from installXY.iso to docs/expat/amd64/xbaseXY.tgz
	+ Copy the xbaseXY.tgz install set from installXY.img to docs/expat/arm64/xbaseXY.tgz
	+ Copy the xfontXY.tgz install set from installXY.iso to docs/fonts/amd64/xfontXY.tgz
	+ Copy the xfontXY.tgz install set from installXY.img to docs/fonts/arm64/xfontXY.tgz
	+ Copy the files under the BOOT partition of installXY.img for the amd64 arch to ~/OpenBSD/X.Y/amd64/BOOT/
	+ Copy the files under the BOOT partition of installXY.img for the arm64 arch to ~/OpenBSD/X.Y/arm64/BOOT/
	+ Download and copy [the Broadcom wifi drivers](https://github.com/pftf/RPi4/tree/master/firmware/brcm) for Raspberry Pi 4 to ~/OpenBSD/X.Y/arm64/firmware/

- Update the configuration files under config with the ones in the new versions of packages:
	+ Also update Doxyfile if the doxygen version has changed

- Update PFRE:
	+ Update PFRE to the current version, support changes in pf if any
	+ Create and install the man2web package
	+ Produce pf.conf.html from pf.conf(5) using man2web
	+ Merge PFRE changes from the previous pf.conf.html, most importantly the anchors

- Update the PHP version numbers in the sources, both php and php-fpm, if upgrading PHP:
	+ config/etc/php-X.Y/
	+ config/etc/php-X.Y.ini
	+ config/pffw.files
	+ config/pffw.mtree
	+ meta/install.sub
	+ config/etc/rc.local
	+ config/etc/syslog.conf
	+ src/Model/system.php
	+ src/View/system/conf.startup.php

- Update phpseclib to its new version if any:
	+ Merge the PFFW changes from the previous version

- Update d3js to its new version if any:
	+ Fix any issues caused by any API changes

- Strip xbase and xfont:
	+ Make sure the contents are the same as in the files in the old iso and img files, except for the version numbers
	+ SECURITY: Be very careful with the permissions of the directories and files in these install sets, they should be the same as the original files

- Run the create script:
	+ Install gettext-tools and doxygen for translations and documentation
	+ Run ./createiso or ./createimg under ~/pffw/
