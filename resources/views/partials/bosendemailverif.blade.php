<button class="text-alert-400 hover:bg-alert-400 hover:text-white p-1 rounded" title="{{ __('Resend Verification Email') }}" wire:click='$emit("openModal", "modal-confirm", {{ json_encode(["title"=>__("Resend Verification Email"), "body"=>__("Send a mail verification request"), "datas"=>["id"=>$id, "name"=>$name, "email"=>$email], "callback" => "resendemailverif"]) }})'><x-icons.at /></button>