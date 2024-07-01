<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;

class MessageLog extends Model
{
    protected $connection = 'pgsql_billiton';
    protected $primaryKey = 'message_id';
    
    protected $quotes = '"';
    //
    protected $fillable = [
        'message_id',
        'terminal_id',
        'service_id',
        'request_time',
        'reply_time',
        'log_id',
        'message_status',
        'request_message',
        'response_message'

    ];

    protected $table = "messagelog";
    
    // protected $table = "public.messagelog as m LEFT JOIN channel.transaction_log t ON m.message_id = t.additional_data
    //                         LEFT JOIN users u ON m.terminal_id::text = u.username::text
    //                         LEFT JOIN terminal trm ON m.terminal_id::text = trm.terminal_id::text
    //                         LEFT JOIN service s ON s.service_id::text = m.service_id::text
    //                         LEFT JOIN ( SELECT tr.stan AS reversal_stan,
    //                                 min(tr.responsecode::text) AS reversal_rc,
    //                                 max(mr.request_time) AS reversal_time,
    //                                 mr.service_id AS reversal_service_id
    //                             FROM messagelog mr
    //                                 LEFT JOIN channel.transaction_log tr ON tr.stan = replace((((mr.request_message::json -> 'msg'::text) -> 'msg_dt'::text))::character varying(255)::text, '". $quotes ."'::text, ''::text)::integer
    //                             WHERE (mr.service_id::text = 'R82561'::text OR mr.service_id::text = 'RA0012'::text OR mr.service_id::text = 'RA0023'::text OR mr.service_id::text = 'RA0033'::text OR mr.service_id::text = 'RA0042'::text OR mr.service_id::text = 'RA0051'::text OR mr.service_id::text = 'RA0063'::text) AND mr.message_status IS NOT NULL AND tr.tx_mti::text = '0400'::text
    //                             GROUP BY tr.stan, mr.service_id) r ON r.reversal_stan = t.stan
                                            
    //                         LEFT JOIN service_data g ON m.message_id::text = g.message_id::text AND (g.name::text = 'fee'::text OR g.name::text = 'margin'::text OR g.name::text = 'dynfee'::text)
                                
    //                         LEFT JOIN service_data gb ON m.message_id::text = gb.message_id::text AND gb.name::text = 'billid'::text";
}
