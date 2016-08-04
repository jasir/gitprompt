#!/usr/bin/env bash

export INCONEMU=$( command -v conemuc >/dev/null 2>&1 && echo '1'; )

function __git_fast_prompt() {
    git status -sb --porcelain -u 2>/dev/null | php -n -q /c/work/projects/bashsettings/gitprompt/gitprompt.php
}

function __generate_prompt() {
    
    ### time + error
    PS1="\n→ \[\e[01;32m\]\t`a=$?; if [ $a = 0 ]; then echo " "; else echo "\[\e[31m\] [$a] "; fi`\[\e[01;33m\]"
    PS1=${PS1}$( __showpaths )
    PS1=${PS1}$( __git_fast_prompt )
    PS1="${PS1}\n\\$ \[$(tput sgr0)\]"
    
    settitle "$( pwdproject )"

    if [ "$INCONEMU" == "1" ] 
    then
	ConEmuC -StoreCWD;
    fi
}

export PROMPT_COMMAND="__generate_prompt;$PROMPT_COMMAND"