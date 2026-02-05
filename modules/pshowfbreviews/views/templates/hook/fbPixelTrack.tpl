{if !empty($eventsToSend)}
    {literal}
        <script>
        	{/literal}{foreach $eventsToSend as $eventToSend}{literal}
            fbq(
                'track',
                '{/literal}{$eventToSend["type"]|escape:'htmlall':'UTF-8'}{literal}',
                {/literal}{$eventToSend["content"] nofilter}{literal},
                {/literal}{$eventToSend["event_data"] nofilter}{literal}
            );
        	{/literal}{/foreach}{literal}
        </script>
    {/literal}
{/if}