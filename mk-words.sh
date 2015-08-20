#!/bin/bash

grep -v '[^ab-def-hij-nop-tuv-z]' /usr/share/dict/american-english | \
	grep '^....\(\|.\|..\|...\|....\|.....\|......\)$' | \
	sort > tmplist

grep 's$' tmplist > tmp-with-s
sed -e 's/s$//' tmp-with-s | sort > tmp-s-removed
comm -12 tmplist tmp-s-removed | sed -e 's/$/s/' > tmp-plurals
grep 'es$' tmplist > tmp-with-es
sed -e 's/es$//' tmp-with-es | sort > tmp-es-removed
comm -12 tmplist tmp-es-removed | sed -e 's/$/es/' >> tmp-plurals
grep 'ies$' tmplist > tmp-with-ies
sed -e 's/ies$/y/' tmp-with-ies | sort > tmp-ies-removed
comm -12 tmplist tmp-ies-removed | sed -e 's/y$/ies/' >> tmp-plurals
cat tmp-plurals | sort > plurals
comm -23 tmplist plurals | sort > wordlist

rm tmplist tmp-with-s tmp-s-removed tmp-with-es tmp-es-removed tmp-with-ies tmp-ies-removed tmp-plurals

#
# First, make a list of all words that end in 'ing' or 'ly'.
# Store them in 'gerunds' and 'adverbs', respectively.
#
grep 'ing$' wordlist > gerunds
grep 'ly$' wordlist > adverbs

#
# Take of the 'ing' from a gerund, and we might have a verb.
#
sed -e 's/ing$//' gerunds | sort > potential-verbs

#
# Potential verbs that are words are probably verbs
#
comm -12 wordlist potential-verbs > verbs1

#
# Add an 'e' and try again
#
sed -i -e 's/$/e/' potential-verbs
sort -o potential-verbs potential-verbs
comm -12 wordlist potential-verbs > verbs2

cat verbs1 verbs2 | sort > verbs

rm potential-verbs verbs1 verbs2

#
# Shorten all of the word files so that they contain
# only words with 4 - 7 characters
#
for f in wordlist gerunds adverbs verbs plurals ; do
  grep '^....\(\|.\|..\|...\|....\|.....\|......\)$' $f | sed -e 's/ch/#/g' > wordtmp
  # Fix as many of the "c" words as possible
  sed -i -e 's/c$/k/g' wordtmp
  sed -i -e 's/cc/ks/g' wordtmp
  sed -i -e 's/cq/kq/g' wordtmp
  sed -i -e 's/ck/k/g' wordtmp
  sed -i -e 's/ct/kt/g' wordtmp
  sed -i -e 's/sce/ske/g' wordtmp
  sed -i -e 's/sci/ski/g' wordtmp
  sed -i -e 's/scy/sky/g' wordtmp
  sed -i -e 's/ci/si/g' wordtmp
  sed -i -e 's/ce/se/g' wordtmp
  sed -i -e 's/cy/sy/g' wordtmp
  sed -i -e 's/ci/si/g' wordtmp
  sed -i -e 's/ce/se/g' wordtmp
  sed -i -e 's/cy/sy/g' wordtmp
  sed -i -e 's/ca/ka/g' wordtmp
  sed -i -e 's/co/ko/g' wordtmp
  sed -i -e 's/cu/ku/g' wordtmp
  # If there are still any 'c's left, get rid of them
  grep -v c wordtmp | sed -e 's/#/ch/g'  | sort > $f
done
rm wordtmp
