Add-Type -AssemblyName System.Drawing

function Pt([float]$x, [float]$y) {
    return New-Object System.Drawing.PointF($x, $y)
}

function New-RoundedRectPath(
    [float]$x,
    [float]$y,
    [float]$w,
    [float]$h,
    [float]$r
) {
    $path = New-Object System.Drawing.Drawing2D.GraphicsPath
    $d = $r * 2
    $path.AddArc($x, $y, $d, $d, 180, 90)
    $path.AddArc($x + $w - $d, $y, $d, $d, 270, 90)
    $path.AddArc($x + $w - $d, $y + $h - $d, $d, $d, 0, 90)
    $path.AddArc($x, $y + $h - $d, $d, $d, 90, 90)
    $path.CloseFigure()
    return $path
}

function Draw-RoundedBox(
    $g,
    [float]$x,
    [float]$y,
    [float]$w,
    [float]$h,
    [string]$text,
    $font,
    $borderPen,
    $fillBrush,
    $textBrush
) {
    $path = New-RoundedRectPath -x $x -y $y -w $w -h $h -r 18
    $g.FillPath($fillBrush, $path)
    $g.DrawPath($borderPen, $path)

    $sf = New-Object System.Drawing.StringFormat
    $sf.Alignment = [System.Drawing.StringAlignment]::Center
    $sf.LineAlignment = [System.Drawing.StringAlignment]::Center
    $rect = [System.Drawing.RectangleF]::new([single]$x, [single]$y, [single]$w, [single]$h)
    $g.DrawString($text, $font, $textBrush, $rect, $sf)
    $path.Dispose()
    $sf.Dispose()
}

function Draw-Cluster(
    $g,
    [float]$x,
    [float]$y,
    [float]$w,
    [float]$h,
    [string]$title,
    $titleFont,
    $borderPen,
    $fillBrush,
    $textBrush
) {
    $path = New-RoundedRectPath -x $x -y $y -w $w -h $h -r 20
    $g.FillPath($fillBrush, $path)
    $g.DrawPath($borderPen, $path)

    $sf = New-Object System.Drawing.StringFormat
    $sf.Alignment = [System.Drawing.StringAlignment]::Center
    $sf.LineAlignment = [System.Drawing.StringAlignment]::Near
    $rect = [System.Drawing.RectangleF]::new([single]$x, [single]($y + 8), [single]$w, [single]32)
    $g.DrawString($title, $titleFont, $textBrush, $rect, $sf)
    $path.Dispose()
    $sf.Dispose()
}

function Draw-Database(
    $g,
    [float]$x,
    [float]$y,
    [float]$w,
    [float]$h,
    [string]$text,
    $font,
    $borderPen,
    $fillBrush,
    $textBrush
) {
    $ellipseH = 26
    $bodyY = $y + ($ellipseH / 2)
    $bodyH = $h - $ellipseH

    $g.FillRectangle($fillBrush, $x, $bodyY, $w, $bodyH)
    $g.FillEllipse($fillBrush, $x, $y, $w, $ellipseH)
    $g.FillEllipse($fillBrush, $x, $y + $h - $ellipseH, $w, $ellipseH)

    $g.DrawEllipse($borderPen, $x, $y, $w, $ellipseH)
    $g.DrawLine($borderPen, $x, $bodyY, $x, $y + $h - ($ellipseH / 2))
    $g.DrawLine($borderPen, $x + $w, $bodyY, $x + $w, $y + $h - ($ellipseH / 2))
    $g.DrawEllipse($borderPen, $x, $y + $h - $ellipseH, $w, $ellipseH)

    $sf = New-Object System.Drawing.StringFormat
    $sf.Alignment = [System.Drawing.StringAlignment]::Center
    $sf.LineAlignment = [System.Drawing.StringAlignment]::Center
    $rect = [System.Drawing.RectangleF]::new([single]($x + 12), [single]($y + 24), [single]($w - 24), [single]($h - 40))
    $g.DrawString($text, $font, $textBrush, $rect, $sf)
    $sf.Dispose()
}

function Draw-Actor($g, [float]$x, [float]$y, $pen) {
    $g.DrawEllipse($pen, $x - 9, $y, 18, 18)
    $g.DrawLine($pen, $x, $y + 18, $x, $y + 48)
    $g.DrawLine($pen, $x - 14, $y + 28, $x + 14, $y + 28)
    $g.DrawLine($pen, $x, $y + 48, $x - 12, $y + 64)
    $g.DrawLine($pen, $x, $y + 48, $x + 12, $y + 64)
}

function Draw-OrthoArrow($g, [System.Drawing.PointF[]]$points, $pen, $arrowBrush) {
    for ($i = 0; $i -lt $points.Length - 1; $i++) {
        $g.DrawLine($pen, $points[$i], $points[$i + 1])
    }

    $end = $points[$points.Length - 1]
    $prev = $points[$points.Length - 2]
    $dx = $end.X - $prev.X
    $dy = $end.Y - $prev.Y
    $len = [Math]::Sqrt(($dx * $dx) + ($dy * $dy))
    if ($len -le 0.01) { return }

    $ux = $dx / $len
    $uy = $dy / $len
    $size = 11.0
    $half = 4.8

    $bx = $end.X - ($ux * $size)
    $by = $end.Y - ($uy * $size)
    $px = -$uy
    $py = $ux

    $p1 = Pt $end.X $end.Y
    $p2 = Pt ($bx + ($px * $half)) ($by + ($py * $half))
    $p3 = Pt ($bx - ($px * $half)) ($by - ($py * $half))
    $g.FillPolygon($arrowBrush, [System.Drawing.PointF[]]@($p1, $p2, $p3))
}

function Draw-Label($g, [string]$text, [float]$x, [float]$y, $font, $textBrush, $bgBrush) {
    $size = $g.MeasureString($text, $font)
    $padX = 5.0
    $padY = 3.0
    $g.FillRectangle($bgBrush, $x, $y, $size.Width + (2 * $padX), $size.Height + (2 * $padY))
    $g.DrawString($text, $font, $textBrush, $x + $padX, $y + $padY)
}

$width = 1880
$height = 1220
$img = New-Object System.Drawing.Bitmap($width, $height)
$g = [System.Drawing.Graphics]::FromImage($img)
$g.SmoothingMode = [System.Drawing.Drawing2D.SmoothingMode]::AntiAlias
$g.TextRenderingHint = [System.Drawing.Text.TextRenderingHint]::AntiAliasGridFit
$g.Clear([System.Drawing.ColorTranslator]::FromHtml("#ececec"))

$linePen = New-Object System.Drawing.Pen([System.Drawing.ColorTranslator]::FromHtml("#333333"), 2)
$borderPen = New-Object System.Drawing.Pen([System.Drawing.ColorTranslator]::FromHtml("#777777"), 2)
$clusterPen = New-Object System.Drawing.Pen([System.Drawing.ColorTranslator]::FromHtml("#4a4a4a"), 2)
$textBrush = New-Object System.Drawing.SolidBrush([System.Drawing.ColorTranslator]::FromHtml("#222222"))
$boxBrush = New-Object System.Drawing.SolidBrush([System.Drawing.ColorTranslator]::FromHtml("#f7f7f7"))
$clusterBrush = New-Object System.Drawing.SolidBrush([System.Drawing.ColorTranslator]::FromHtml("#e8e8e8"))
$labelBg = New-Object System.Drawing.SolidBrush([System.Drawing.ColorTranslator]::FromHtml("#ececec"))

$fontTitle = New-Object System.Drawing.Font("Arial", 16, [System.Drawing.FontStyle]::Bold)
$fontCluster = New-Object System.Drawing.Font("Arial", 14, [System.Drawing.FontStyle]::Bold)
$fontNode = New-Object System.Drawing.Font("Arial", 11, [System.Drawing.FontStyle]::Regular)
$fontLabel = New-Object System.Drawing.Font("Arial", 10, [System.Drawing.FontStyle]::Regular)

# Node coordinates
$frontend = [pscustomobject]@{x=90;y=160;w=400;h=84}
$backend = [pscustomobject]@{x=50;y=300;w=1060;h=560}
$api = [pscustomobject]@{x=140;y=385;w=310;h=92}
$logsvc = [pscustomobject]@{x=540;y=385;w=530;h=92}
$rms = [pscustomobject]@{x=120;y=550;w=330;h=92}
$alert = [pscustomobject]@{x=120;y=700;w=390;h=92}
$external = [pscustomobject]@{x=1140;y=385;w=700;h=230}
$iot = [pscustomobject]@{x=1180;y=470;w=620;h=78}
$db = [pscustomobject]@{x=580;y=980;w=500;h=120}

# Draw actors and containers
Draw-Actor $g 255 20 $linePen
Draw-Label $g "Admin / Teknisi" 170 90 $fontNode $textBrush $labelBg
Draw-RoundedBox $g $frontend.x $frontend.y $frontend.w $frontend.h "Aplikasi Dashboard Monitoring`n(Frontend - Blade Laravel)" $fontNode $borderPen $boxBrush $textBrush
Draw-Cluster $g $backend.x $backend.y $backend.w $backend.h "Backend Laravel 12 (PHP)" $fontCluster $clusterPen $clusterBrush $textBrush
Draw-Cluster $g $external.x $external.y $external.w $external.h "Sistem Eksternal" $fontCluster $clusterPen $clusterBrush $textBrush
Draw-RoundedBox $g $api.x $api.y $api.w $api.h "REST API`n(JSON Request/Response)" $fontTitle $borderPen $boxBrush $textBrush
Draw-RoundedBox $g $logsvc.x $logsvc.y $logsvc.w $logsvc.h "Log Activity Service`n(Pencatatan Aktivitas User)" $fontNode $borderPen $boxBrush $textBrush
Draw-RoundedBox $g $rms.x $rms.y $rms.w $rms.h "RMS/FFT Engine`n(Pengolahan Data Getaran)" $fontNode $borderPen $boxBrush $textBrush
Draw-RoundedBox $g $alert.x $alert.y $alert.w $alert.h "Alert Processor`n(Evaluasi Threshold & Generate Alert)" $fontNode $borderPen $boxBrush $textBrush
Draw-RoundedBox $g $iot.x $iot.y $iot.w $iot.h "Sensor IoT (ESP32 - Getaran & Suhu, Data Mentah)" $fontNode $borderPen $boxBrush $textBrush
Draw-Database $g $db.x $db.y $db.w $db.h "Database Internal (MySQL)`n(User, Sensor, RMS/FFT, Alert, Log)" $fontNode $borderPen $boxBrush $textBrush

# Edges with labels
Draw-OrthoArrow $g ([System.Drawing.PointF[]]@( (Pt 255 110), (Pt 255 160) )) $linePen $textBrush
Draw-Label $g "Akses Dashboard" 110 126 $fontLabel $textBrush $labelBg

Draw-OrthoArrow $g ([System.Drawing.PointF[]]@( (Pt 255 244), (Pt 255 385) )) $linePen $textBrush
Draw-Label $g "HTTP Request`n(GET/POST JSON)" 150 250 $fontLabel $textBrush $labelBg

Draw-OrthoArrow $g ([System.Drawing.PointF[]]@( (Pt 185 385), (Pt 185 315), (Pt 235 315), (Pt 235 244) )) $linePen $textBrush
Draw-Label $g "JSON Response`n(Data)" 70 330 $fontLabel $textBrush $labelBg

Draw-OrthoArrow $g ([System.Drawing.PointF[]]@( (Pt 450 431), (Pt 540 431) )) $linePen $textBrush
Draw-Label $g "Catat Aktivitas User" 445 392 $fontLabel $textBrush $labelBg

Draw-OrthoArrow $g ([System.Drawing.PointF[]]@( (Pt 220 477), (Pt 220 550) )) $linePen $textBrush
Draw-Label $g "Kirim Data Mentah" 42 500 $fontLabel $textBrush $labelBg

Draw-OrthoArrow $g ([System.Drawing.PointF[]]@( (Pt 290 642), (Pt 290 700) )) $linePen $textBrush
Draw-Label $g "Kirim RMS/FFT" 145 658 $fontLabel $textBrush $labelBg

Draw-OrthoArrow $g ([System.Drawing.PointF[]]@( (Pt 1180 530), (Pt 1115 530), (Pt 1115 465), (Pt 450 465) )) $linePen $textBrush
Draw-Label $g "HTTP POST JSON`n(Data Mentah)" 900 536 $fontLabel $textBrush $labelBg

Draw-OrthoArrow $g ([System.Drawing.PointF[]]@( (Pt 450 404), (Pt 1115 404), (Pt 1115 509), (Pt 1180 509) )) $linePen $textBrush
Draw-Label $g "HTTP Response`n(200 OK)" 860 376 $fontLabel $textBrush $labelBg

Draw-OrthoArrow $g ([System.Drawing.PointF[]]@( (Pt 360 477), (Pt 360 890), (Pt 680 890), (Pt 680 980) )) $linePen $textBrush
Draw-Label $g "Simpan Data Sensor`n(Data Mentah)" 370 855 $fontLabel $textBrush $labelBg

Draw-OrthoArrow $g ([System.Drawing.PointF[]]@( (Pt 450 595), (Pt 760 595), (Pt 760 980) )) $linePen $textBrush
Draw-Label $g "Simpan Data Sensor`n(RMS & FFT)" 510 610 $fontLabel $textBrush $labelBg

Draw-OrthoArrow $g ([System.Drawing.PointF[]]@( (Pt 310 792), (Pt 310 940), (Pt 850 940), (Pt 850 980) )) $linePen $textBrush
Draw-Label $g "Simpan Alert" 480 920 $fontLabel $textBrush $labelBg

Draw-OrthoArrow $g ([System.Drawing.PointF[]]@( (Pt 760 477), (Pt 760 865), (Pt 1000 865), (Pt 1000 980) )) $linePen $textBrush
Draw-Label $g "Simpan Log Aktivitas User" 820 840 $fontLabel $textBrush $labelBg

$output = Join-Path (Get-Location) "docs/arsitektur-sistem-monitoring.png"
$img.Save($output, [System.Drawing.Imaging.ImageFormat]::Png)

# Cleanup
$fontTitle.Dispose()
$fontCluster.Dispose()
$fontNode.Dispose()
$fontLabel.Dispose()
$linePen.Dispose()
$borderPen.Dispose()
$clusterPen.Dispose()
$textBrush.Dispose()
$boxBrush.Dispose()
$clusterBrush.Dispose()
$labelBg.Dispose()
$g.Dispose()
$img.Dispose()

Write-Output "Generated: $output"
