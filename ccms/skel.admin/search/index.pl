#!/usr/bin/perl

# $Id: index.pl,v 1.1 2003/09/17 12:40:55 terraces Exp $
# Copyright (C) 2001,2002, Makina Corpus, http://makinacorpus.org
# Hacked from Enobin search engine <http://shouting-myke.hypermart.net/enobin>
# By Peter Sergeant <pete_sergeant@hotmail.com>
# This file is a component of CCMS <http://makinacorpus.org/index.php/ccms>
# Created and maintained by mose <mose@makinacorpus.org>
# Released under GPL version 2 or later see LICENSE file
# or http://www.gnu.org/copyleft/gpl.html

# enobin search v0.9
# index.pl
# This script creates the two index files and the offset file that are
# searched by parse.pl
# PRAGMAtism, not idealism
use strict;
use File::Find;
$| = 1;
##
# Customs - Anything to declare?
# Interface
my $interface = 0; # 0 is HTML, 1 is text, 2 in interactive text
# Where to read the data from (no trailing slash)
my	$retrieved_dbs;
# Where to put the index files
my	$retrieved_source;
# The regex to use with File-Find
my $file_regex = '^(\/[a-zA-Z0-9][^\/]*)*\/[a-zA-Z0-9][^\/]*$';
# Regex for picking out the title (collects $1)
my $title_regex = '<TITRE>(.+?)</TITRE>';
# Weighting Structure
my @weight_array = (
	['<TITRE>(.+?)</TITRE>', '20'],
	['<AUTEUR>(.+?)<\/AUTEUR>', '5'],
	['<CHAPEAU>(.+?)<\/CHAPEAU>', '5'],
	['<CONTENU>(.+?)<\/CONTENU>', '5'],
);
my %data_hash;			# Where we collect the index db before splurging
						# it back out
my $count;				# So that we know where we are during the
						# indexing
my @list;				# To store the files we find
my $file_found_count;	# Stores the total number of files we'll be
						# dealing with
my @data_hash_keys;		# To store %data_hash's keys in, no doubt
my $index_db_name	=	'index_file.db';
my $file_db_name	=	'file.db';
my $file_offsets_name=	'file_offsets.db';
my $index_offsets_name=	'index_offsets.db';
##

# Get source dir and index files directory from parameter
if ($interface == 0) {
	# We are being run through a web index
	# This means we need to parse the query_string
	print "Content-type: text/html\n\n";
	my @pairs = split(/\&/, $ENV{'QUERY_STRING'});
    my %arguments_hash;	# Where we keep the data we got
                			# from the query_string
	foreach my $pair (@pairs) {
    	my ($key, $value) = split(/=/, $pair);
		$key =~ tr/+/ /;
		$value =~ tr/+/ /;
		$key =~ s/%([a-fA-F0-9][a-fA-F0-9])/pack("C", hex($1))/eg;
		$value =~ s/%([a-fA-F0-9][a-fA-F0-9])/pack("C", hex($1))/eg;
		$arguments_hash{$key} = $value;
	}
	# Read in what we need
	$retrieved_dbs = $arguments_hash{'dbs'};
	$retrieved_source = $arguments_hash{'src'};
}

# Dir where files are located, according lang & domain parameters
my $db_dir = "dbs" . $retrieved_dbs;
my $source_dir = $retrieved_source; 

# Hello and welcome to tonight's performance
print "\n=--=--=--=--=--=--=--=--=--=--=--=--=--=--=--=-\n";
print "enobin Search v0.9 - index.pl - Indexer running\n";
print "=--=--=--=--=--=--=--=--=--=--=--=--=--=--=--=-\n\n";

# Get rid of garbage
&status("Deleting Old Index Files");
unlink "$db_dir/$file_db_name", "$db_dir/$index_db_name",
	"$db_dir/$file_offsets_name", "$db_dir/$index_offsets_name";
&status(1);
##

# Get our file list and give it to @list
&status("Reading Directory");

find(\&wanted, $source_dir);
sub wanted {
	if ($File::Find::name =~ m/$file_regex/i) {
		my $file_found = $File::Find::name;
		$file_found =~ s/$source_dir//i; # Trim the full dir
		push(@list, $file_found);
	}
}

$file_found_count = @list; # Get a numeric count of files we found
&status(1);
&status("* $file_found_count files found");
##

# DAS ACTION-MAN ROUTINES!
# This is wear it all happens
#
# Open our file index first
open(FILEDB, ">$db_dir/$file_db_name")||die;
foreach my $item (@list) {
	$count++;	# Increment our score keeper
	&status("\n=> Processing $item ($count of $file_found_count)");

	# Some files take forever and ever and ever
	# so we find out here how big they are...
	my $fsize = (stat("$source_dir$item"))[7];
	$fsize = int($fsize/1024);

	# Read the file, and return a title and a list of words
	&status("=> Reading $fsize KB");
	my ($title, $words) = @{&fileclean("$source_dir$item", $title_regex)};
	&status(1);

	# Show the viewer the title
	&status("=> Title is $title");
	&status("=> Adding entries to the words database");
	%data_hash = %{&indexwords("$count",$words,\%data_hash)};
	&status(1);
	&status("=> Adding a record for the file");
	$words =~ s/startherecode.*//g;
	print FILEDB "$item |$title| $words\n";
	&status(1);
	&status("=> All done");
}
close FILEDB;
##

# Recap where we are, and sort our words in @data_hash_keys
&status("\nHaving read all the files, we now need to create $index_db_name");
&status("Sorting Keywords");
@data_hash_keys = (keys %data_hash);
@data_hash_keys = sort(@data_hash_keys);
&status(1);
##

# Write the index to file
&status("Writing $index_db_name to file");
my @offsets_index;
open(OUTIND, ">$db_dir/$index_db_name")||die;
foreach my $entry (@data_hash_keys) {
	push(@offsets_index, tell(OUTIND));
	print OUTIND "$entry $data_hash{$entry}\n";
}
&status(1);
##

# Print these index offsets to disk
&status("Committing offsets to disk");
open(FILE, ">$db_dir/$index_offsets_name")||die;
print FILE join "\n", @offsets_index;
close FILE;
&status(1);
##

# Calculate new line offsets for the file db
&status("Calculating Offsets for $file_db_name");
open(FILE, "<$db_dir/$file_db_name")||die;
my @offsets_file;
while (<FILE>) {
    push (@offsets_file, tell(FILE)-length);
}
close FILE;
&status(1);
##

# Print these file offsets to disk
&status("Committing offsets to disk");
open(FILE, ">$db_dir/$file_offsets_name")||die;
print FILE join "\n", @offsets_file;
close FILE;
&status(1);
##

# Bye!
print "\n$count files indexed. index.pl finished.\n";
print "Index files written. Stay happy.\n";
exit;
##

# SUB-PEN.
# Of post-Soviet-Russian variety, with Chechens and a whole bunch of
# paintable action figures so you can recreate your very own news
# broadcast! Parents, dare you deny your children this fabulous
# opportunity of becoming journalists? Too much caffine has been had.


# Opens file and returns with a title and a nice wordstring
# fileclean("$file", "$title_regex");
sub fileclean {
	my $title;	# Container for the title
	my $file;	# We keep the file in here and exact some nasty
				# regexes on it. Mwahahahaha.

	open(FILE, "<$_[0]")||die "$!";
	$file = join '', <FILE>;
	close FILE;

	# It simply is too cold today. :-( I'm wearing a wooly hat indoors,
	# and it's only November. Evidently more coffee/caffine is needed.

	# One caffine hit and ham sarnie later...
	if ($file =~ m/$_[1]/sig) {
		($title = $1) =~ s/(\s+|\|)/ /gs;
	}
	
	$file .= " startherecode ";
	my $weight_string;
	
	foreach my $weight_current (@weight_array) {
		my ($weight_regex, $weight_number) = @{$weight_current};
		while ($file =~ m/$weight_regex/ig) {
			$weight_string .= ($1 ." ") x $weight_number;	
		}
	}
	$file .= $weight_string;
	
	# Let's save some time...
	$_ = $file;
	# Lower Case to save on /i modifiers
	tr/A-Z/a-z/;
	# Same idea for whitespace
	s/\s+/ /sg;
	# Remove Tags
	s/<(?:[^>'"]*|".*?"|'.*?')+>//g;
	# Remove nasty ampersand punctutation
	s/&(\x23\d+|\w+);?//g;
	s/'//g;
	# Hell, remove anything that isn't a letter :)
	s/[^a-z1-9 ]/ /g;
	# Apparently this needs to be run twice. It deeply upsets me
	# that I don't know why...
	s/\s+/ /sg;
	# Now lets return an array reference with everything in...
	return [$title, $_];
}

sub indexwords {
	my @words;	# Neatf word list
	my $word;	# Token used to process @words
	my %data_hash = %{$_[2]};

	# Just to make sure noone tries to invoke the sub without a
	# string to add and the location of our index file.
	@words = split(/\s+/, $_[1]);
	@words = sort(@words);

	my $prev = 'nonesuch';
	@words = grep($_ ne $prev && ($prev = $_), @words);

	foreach $word (@words) {
		my $word_count;
		while ($_[1] =~ m/$word/g) {
			$word_count++;
		}
		$data_hash{$word} .=  "$_[0]|$word_count ";
	}
	return \%data_hash;
}


sub status {
	if ($_[0] =~ m/[a-z]/i) {
		print "\n$_[0]...\t";
	} else {
		print "- Done";
	}
}

