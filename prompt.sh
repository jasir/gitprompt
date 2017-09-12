#!/usr/bin/env bash

export INCONEMU=$( command -v conemuc >/dev/null 2>&1 && echo '1'; )

ESC=$(printf '\033')
MARKER="${ESC}[?7711h"

function __git_fast_prompt() {
    git status -sb --porcelain -u 2>/dev/null | php -n -q /c/work/projects/bashsettings/gitprompt/gitprompt.php
}

function __generate_prompt() {
    ### ESC]9;12ESC\
	### ESC[?7711h
    ### time + error code

    PS1="${MARKER}\n→ \[\e[01;32m\]\t$( a=$?; if [ $a = 0 ]; then echo " "; else echo "\[\e[31m\] [$a] "; fi )\[\e[01;33m\]"

    ### conemu marker ESC]9;12ESC\
    ### PS1="${PS1}${ESC}[?7711h"
    ### PS1="${PS1}"

    ### paths (splitted to git root)
    PS1=${PS1}$( __showpaths )



    ### git status
    PS1=${PS1}$( __git_fast_prompt )

    PS1="${PS1}"

    ### reset
    PS1="${PS1}\n\\$ \[$(tput sgr0)\]"



    settitle "$( pwdproject )"

    if [ "$INCONEMU" == "1" ] 
    then
	ConEmuC -StoreCWD;
    fi
}

export PROMPT_COMMAND="__generate_prompt;$PROMPT_COMMAND"