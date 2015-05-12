<li class="dropdown dropdown-extended dropdown-inbox" id="header_inbox_bar">
    <a href="#" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
        <i class="icon-envelope-open"></i>
        <!-- BEGIN QTDE_NOVA_MENSAGEM -->
        <span class="badge badge-default">{totalMensagens}</span>
        <!-- END QTDE_NOVA_MENSAGEM -->
    </a>
    <ul class="dropdown-menu">
        <li class="external">
            <h3>Novas Mensagens</h3>
            <a href="{BASE_URL}contato"> ver todos</a>
        </li>
        <li>
            <ul class="dropdown-menu-list scroller" style="height: 275px;" data-handle-color="#637283">
                <!-- BEGIN LISTA -->
                <li>
                    <a href="{BASE_URL}contato/ver/{codigo}">
                        <span class="subject">
                            <span class="from">{nome}</span>
                            <span class="time">{data}</span>
                        </span>
                        <span class="message">{mensagem}</span>
                    </a>
                </li>
                <!-- END LISTA -->
            </ul>
        </li>
    </ul>
</li>

