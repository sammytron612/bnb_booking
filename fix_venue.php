<?php
$file = 'c:\Laravel_new\bnb\resources\views\livewire\bookings-table.blade.php';
$content = file_get_contents($file);
$content = str_replace('{{ $booking->venue }}', '{{ $booking->venue->venue_name }}', $content);
file_put_contents($file, $content);
echo "Replacement completed\n";
?>
