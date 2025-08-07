<h2>{{ $title }}</h2>
<form action="/{{ $context }}/backup/delete" method="post">
    @csrf
    <table class="table table-bordered shadow table-striped align-middle">
        <thead class="table-dark">
        <tr>
            @if($count > 1)
                <th class="text-center" scope="col" style="width: 50px">
                    <input type="checkbox" name="checkall">
                </th>
            @endif
            <th scope="col">Nome file</th>
            <th scope="col">Dimensione</th>
            <th scope="col">Tipologia</th>
            <th scope="col">Data creazione</th>
            <th colspan="2" scope="col" style="width: 110px">&nbsp;</th>
        </tr>
        </thead>

        @if($count > 1)
            <tfoot>
            <tr>
                <td colspan="7">
                    <div class="btn-group">
                        <button type="button" class="btn btn-danger dropdown-toggle"
                                data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-trash"></i>
                            Cancella tutti i selezionati
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end bg-danger shadow">
                            <li>
                                <button type="submit" class="dropdown-item bg-danger text-white">
                                    Conferma cancellazione
                                </button>
                            </li>
                        </ul>
                    </div>
                </td>
            </tr>
            </tfoot>
        @endif

        <tbody class="table-group-divider">
        @forelse($items as $row)
            <tr>
                @if($count > 1)
                    <td class="text-center">
                        <input type="checkbox" name="files[]" value="{{ $row['filename'] }}">
                    </td>
                @endif
                <th scope="row">{{ $row['filename'] }}</th>
                <td>{{ $row['filesize'] }}</td>
                <td>{{ $row['filetype'] }}</td>
                <td class="text-end">{{ $row['lastModified'] }}</td>
                <td style="width: 20px" class="text-center">
                    <a class="btn btn-sm btn-info"
                       href="/{{ $context }}/backup/{{ $row['filename'] }}/download"
                    >
                        <i class="bi bi-download"></i>
                    </a>
                </td>
                <td style="width: 20px" class="text-center">
                    <div class="btn-group">
                        <button type="button" class="btn btn-sm btn-danger dropdown-toggle"
                                data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-trash"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end bg-danger shadow">
                            <li>
                                <a class="dropdown-item bg-danger text-white"
                                   href="/{{ $context }}/backup/{{ $row['filename'] }}/delete">
                                    Conferma cancellazione
                                </a>
                            </li>
                        </ul>
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="7" class="py-5">
                    Non ci sono file di backup disponibili.
                </td>
            </tr>
        @endforelse
        </tbody>
    </table>
</form>
<script>
    const checkAll = document.querySelector('input[name="checkall"]');
    checkAll.addEventListener('click', function () {
        const checkboxes = document.querySelectorAll('input[name="files[]"]');
        checkboxes.forEach(function (checkbox) {
            checkbox.checked = checkAll.checked;
        });
    });
</script>
