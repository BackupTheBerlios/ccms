#!/usr/bin/perl

# $Id: parse.pl,v 1.1 2003/09/17 12:40:55 terraces Exp $
# Copyright (C) 2001,2002, Makina Corpus, http://makinacorpus.org
# Hacked from Enobin search engine <http://shouting-myke.hypermart.net/enobin>
# By Peter Sergeant <pete_sergeant@hotmail.com>
# This file is a component of CCMS <http://makinacorpus.org/index.php/ccms>
# Created and maintained by mose <mose@makinacorpus.org>
# Released under GPL version 2 or later see LICENSE file
# or http://www.gnu.org/copyleft/gpl.html

# enobin - Search
#	search.pl
# Pragmas and friends
use strict;
use Search::Dict;
$| = 1;		# Autoflushing
# Customs - "Anything to Declare?"
# Interface
my $interface = 0; # 0 is HTML, 1 is text, 2 in interactive text
# Path appended to the filename in the results output
my $result_path = "file:/usr/doc/FAQ";
# Script URL, which only needs to be set if you're running as HTML
my $script_url = "http://127.0.0.1/cgi-bin/parse.pl";
# Names of databases
my $index_db_name = "index_file.db";
my $file_db_name = "file.db";
my $index_offsets_name = "index_offsets.db";
my $file_offsets_name = "file_offsets.db";
# Default Place to start results from - this can be over-ridden
#	when the script is called by HTML and or interactive mode
my $start_position = 1;
# Default Number of hits to retrieve (again, can be over-ridden)
my $results_to_retrieve = 6;
# Other declarations that you don't need to worry about
my @weigher;		# Holds searched terms deemed to be relevent for weighting
my @weight_array;	# Holds pages and their weights to be sorted
my @phrases;       	# Holds "phrases"
my $phrase_flag;	# Tells the program whether we have "phrases"
my $retrieved_search_terms;		# Terms that we found
my $retrieved_number_of_results;	# Self explanitary
my $retrieved_start_position;		# Again, self explanitary
our $retrieved_domain;		# Again, self explanitary
our $retrieved_lang;		# Again, self explanitary
my $original_terms;			# Used with HTML output
my @sumterms;			# Contains terms that we use in the summary
##

# Retrieve the search terms and start_position for results
if ($interface == 0) {
	# We are being run through a web index
	# This means we need to parse the query_string
	print "Content-type: text/html\n\n";
	my @pairs = split(/\&/, $ENV{'QUERY_STRING'});
    my %arguments_hash;	# Where we keep the data we got
                			# from the query_string
	foreach my $pair (@pairs) {
    	my ($key, $value) = split(/=/, $pair);
		if ($key eq "terms") { $original_terms = $value; }
		$key =~ tr/+/ /;
		$value =~ tr/+/ /;
		$key =~ s/%([a-fA-F0-9][a-fA-F0-9])/pack("C", hex($1))/eg;
		$value =~ s/%([a-fA-F0-9][a-fA-F0-9])/pack("C", hex($1))/eg;
		$arguments_hash{$key} = $value;
	}
	# Read in what we need
	$retrieved_search_terms	= $arguments_hash{'terms'};
	$retrieved_domain = $arguments_hash{'domain'};
	$retrieved_lang	= $arguments_hash{'lng'};
	$retrieved_number_of_results = $arguments_hash{'return'} || $results_to_retrieve;
	$retrieved_start_position = $arguments_hash{'start'} || $start_position;
}

# The index is in lower-case, so to stop thing getting 'crazy' we make sure
# that the search terms are too.
$retrieved_search_terms = lc($retrieved_search_terms);

# Dir where files are located, according lang & domain parameters
my $db_dir = "dbs" . $retrieved_domain . $retrieved_lang;

# Retrieve offsets for the file database
open(PART, "<$db_dir/$file_offsets_name")||die;
my @positions = <PART>;
close PART;
chomp(@positions);
##

# Micro-optimisation by opening file_db just the once
open(WINDEX, "<$db_dir/$file_db_name")||die;
##

# Retrieve a list of feasible pages and their weights, and
# make a hash of their weights, then sort them
my @page_list = @{&parse_terms($retrieved_search_terms)};
my %final_weight_hash = %{&weight_hash(\@weigher)};
foreach my $page (@page_list) {
	push(@weight_array, sprintf("%010d", $final_weight_hash{$page}) . "|" . $page);
}
@weight_array = reverse(sort(@weight_array));
##

if ($phrase_flag) {
    # Create a new array that will replace the weight_array
    my @new_weight_array;
    # Go through the existing entries in @weight_array
 	foreach my $current_file (@weight_array) {
    	# Retrieve info we need about the file
    	my ($weight, $file) = split(/\|/, $current_file);
    	my @returned_parts = @{&fileinfo($file, "$db_dir/$file_db_name")};
    	# How many correct matches we get for the phrases, that we can later
    	# compare to how many we should have.
    	my $counts;

    	foreach my $phrase (@phrases) {
    		my $flag;	# Flag to say if we found the phrase
    		my ($bool, $regex) = @{$phrase};
    		# Decide if we have the phrase
    		if ($returned_parts[2] =~ m/$regex/) {
    			$flag = 1;
    		}
    		# If we wanted the phrase and we found it, or if we didn't want
    		# the phrase and didn't find it, increment $counts
    		if (($flag)&&($bool ne "-")) {
    			$counts++;
    		} elsif (($flag != 1)&&($bool eq "-")) {
    		 	$counts++;
    		}
    	}

    	# Now, should all the phrases we wanted have matched properly,
    	# then $counts should have the same value as the size of
	    # @phrases itself.
    	my $num_of_terms_needed = @phrases;

	    # If it is, then we count the page, and put it into what will
    	# become the new @weight_array
    	if ($counts == $num_of_terms_needed) {
    		push(@new_weight_array, $current_file);
    	}
    }
    @weight_array = @new_weight_array;
}

# Some magic for determining some figures we print out later
my $total_results = @weight_array;
my $last_result = $retrieved_start_position + $retrieved_number_of_results;
if ($last_result > $total_results) { $last_result = $total_results + 1; }
##

# No result handler
if ($start_position > $total_results) {
	exit;
}
##

# Get a copy of the part of @weight_array that we want.
@weight_array = splice(@weight_array, ($retrieved_start_position - 1), ($retrieved_number_of_results));
##

# Cycle through weight_array and print our results
my $numeric_result_counter = $retrieved_start_position;

# Print out how many we found and links to other pages
my $number_of_pages_option = int($total_results/10);
if ($number_of_pages_option > 10) {$number_of_pages_option = 10};
my $page_display_counter;
if ($number_of_pages_option) { $number_of_pages_option++; }
##

print "$total_results\n";
foreach my $result (@weight_array) {
	my ($weight, $file) = split(/\|/, $result);
	$weight =~ s/^0+//;
	my ($title, $file_name, $text) = @{&fileinfo($file, "$db_dir/$file_db_name")};
	my ($summary1, $summary2);

	if ($text =~ m/((?:^| ).{0,30}(?:^| )$sumterms[0](?:$| ).{0,30}(?:$| ))/) { $summary1 = "\n<BR>" . $1; }
	if ($text =~ m/((?:^| ).{0,30}(?:^| )$sumterms[1](?:$| ).{0,30}(?:$| ))/) { $summary2 = "\n<BR>" . $1; }

	$summary1 =~ s/((?:^| )$sumterms[0](?:$| ))/<B>$1<\/B>/;
	$summary2 =~ s/((?:^| )$sumterms[1](?:$| ))/<B>$1<\/B>/;

	print "$retrieved_domain---$retrieved_lang---$file_name+++$title\n";
	$numeric_result_counter++;
}
##

# A subroutine that will return a list of all files containing a keyword
sub wordlook {
	my $line;	# Temp holder

	# Just to make sure noone tries to invoke the sub without a
	# search term...
	$_[0] || die("You didn't specify a term to look up.");
	$_[1] || die("You didn't specify the location of the index file.");

	open(INDEX, "$_[1]")||
		die("Couldn't open $_[1]");
	look *INDEX, "$_[0]";
	$line = <INDEX>;
	close INDEX;

	if ($line =~ m/$_[0]/i) {
		$line =~ s/$_[0] //;
		return [split(/\s+/, $line)];
	}
	else { return; }
}

sub weight_hash {
	my @word_list = @{$_[0]};
	my %weight_hash;

	# Goes through each term
	foreach my $word (@word_list) {
		# Goes through each returned file
		my @files = @{&wordlook($word, "$db_dir/$index_db_name")};
		foreach my $word (@files) {
			my ($file, $weight) = split(/\|/, $word);
			$weight_hash{$file} = ($weight_hash{$file} + 1) * $weight;
		}
	}
	return \%weight_hash;
}

sub fileinfo {
	my $line;
 	# Just to make sure noone tries to invoke the sub without an
	# array ref...
	$_[1] || die("You didn't specify an index file.");
	$_[0] || die("You didn't specify a file index.");

	my $index = $_[0];
	seek WINDEX, $positions[$index - 1], 0;
	$line = <WINDEX>;
	my ($title, $fname, $wordy_part);

	if ($line =~ m/^(.+?) \|(.+)\|.+$/) {
		$title = $2;
		$fname = $1;
	}
	return [$title, $fname];
}


sub parse_terms {
	my $terms = $_[0];
	$terms =~ s/S\|P/ /g;
	$terms =~ s/\|BR/\)/g;
	#$terms =~ s/
	# Substitute word descriptors to the + variety
	if ($terms !~ /^(and |not |or )/i) { $terms = "+" . $terms; }
	$terms = " " . $terms . " ";
	$terms =~ s/\s*and\s*/ \+/g;
	$terms =~ s/\s*not\s*/ \-/g;
	$terms =~ s/\s*or\s*/ \~/g;

	while ($terms =~ m/\(/) {
		my $returned = extract($terms);
        my $old_returned = quotemeta $returned;
		$returned =~ s/\(/\|BL\|/g;
		$returned =~ s/\)/\|BR\|/g;
		$returned =~ s/\s+/\|SP\|/g;
		$terms =~ s/\($old_returned\)/$returned/;
	}

	while ($terms =~ m/([\+\-\~])?(\s*)\"(.+?)\"/) {
		my $old_string = quotemeta "$1$2\"$3\"";
		my $subs = "$3";
		my $bool = $1;
		push(@phrases, [$bool, $3]);
		my @inner_terms = split(/\s+/, $subs);
		my $new_string;
		if ($bool ne "-") {
			$new_string = " $bool" . join " $bool", @inner_terms;
		}
		$terms =~ s/$old_string/$new_string/;
		$phrase_flag = 1;
	}

	# Populate @terms with the search terms
	my @terms = grep($_ =~ m/\w/, (split(/\s+/, $terms)));
	my @final_list = ();
	my $main_and_flag = 0;
	my $clean_flag = 1;
	if ($terms[0] =~ m/^\+/) { $main_and_flag++; }

	foreach my $term (@terms) {
		my $type;
		# Assign Booleaan
		if ($term =~ s/^(\W)//) { $type = $1; }
		if ($type ne "-") { push(@sumterms, $term) };
		my @words;
		if ($term =~ m/\|SP\|/) {
			$term =~ s/\|SP\|/ /g;
			@words = @{&parse_terms($term)};
			$term = '';
		} elsif (&wordlook($term, "$db_dir/$index_db_name")) {
			@words = @{&wordlook($term, "$db_dir/$index_db_name")};
			unless ($type eq "-") { push(@weigher, $term);}
		}

		foreach (@words) { $_ =~ s/\|(.*)//;  }
		
        if ($clean_flag) {@final_list = @words; $clean_flag=0;}
		if ($type eq '+') { @final_list = @{&xor_array(\@words, \@final_list)}; }
		if ($type eq '-') { @final_list = @{&sub_array(\@final_list, \@words)}; }
		if ($type eq '~') { @final_list = @{&add_array(\@final_list, \@words)}; }
	}
	return \@final_list;
}

sub extract {
    my $string = $_[0];
    my @chars = split(//, $string);
    my $count;
    my $br_exist_flag;
    my $buffer;
    foreach my $char (@chars) {
        if ($char eq "(") {
            $count++; $br_exist_flag = 1;
        } elsif ($char eq ")") {
            $count--;
        }
        if ($br_exist_flag) { $buffer .= $char; }

        if (($br_exist_flag)&&($count ==0)) {
                $buffer =~ s/^\(//;
                $buffer =~ s/\)$//;
                return $buffer;
        }
    }
}

sub add_array {
	my $prev = 'nonesuch';
	my @total_first = (@{$_[0]}, @{$_[1]});
	@total_first = sort(@total_first);
	my @return_array = grep($_ ne $prev && ($prev = $_), @total_first);
	return \@return_array;
}

sub xor_array {
	my $prev = 'nonesuch';
	my @total_first = (@{$_[0]}, @{$_[1]});
	@total_first = sort(@total_first);
	my $current;
	my @return_array;
	foreach $current (@total_first) {
		if ($prev eq $current) { push(@return_array, $prev);  }
		$prev = $current;
	}
	@return_array = @{&add_array(\@return_array, \@return_array)};
	return \@return_array;
}

sub sub_array {
	my @a = @{$_[0]};
	my @b = @{$_[1]};
	my %hash = map {$_ => 1} @a;
	my $current;
	foreach $current (@b) {
		delete $hash{$current};
	}
	@a = keys %hash;
	@a = sort(@a);
	return \@a;
}
