<?php

function modfagi_getdestinfo($dest)
{
    return FreePBX::Modfagi()->getDestinationInfo($dest);
}

function modfagi_check_destinations($dest = true)
{
    return FreePBX::Modfagi()->checkDestinations($dest);
}

function modfagi_destinations()
{
    return FreePBX::Modfagi()->getDestinations();
}
