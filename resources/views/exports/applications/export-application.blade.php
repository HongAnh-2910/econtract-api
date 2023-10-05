<table>
    <thead>
    <tr>
        <th style="width: 50px">STT</th>
        <th style="width: 150px">Người tạo</th>
        <th style="width: 150px">Mã đơn từ</th>
        <th style="width: 150px">Họ và tên</th>
        <th style="width: 150px">Trạng thái</th>
        <th style="width: 150px">Lý do</th>
        <th style="width: 150px">Phòng ban</th>
        <th style="width: 150px">Vị trí</th>
        <th style="width: 150px">Đính kèm</th>
        <th style="width: 150px">Số ngày</th>
        <th style="width: 150px">Ngày tạo</th>
    </tr>
    </thead>
    <tbody>
    @if(count($applications) > 0)
        @php $stt = 0 @endphp
        @foreach($applications as $application)
            @php $stt ++ @endphp
            <tr>
                <td style="text-align: left">{{ $stt }}</td>
                <td style="text-align: left">{{ $application->name }}</td>
                <td style="text-align: left">{{ $application->code }}</td>
                <td style="text-align: left">{{ $application->name }}</td>
                <td style="text-align: left">{{ \App\Enums\ApplicationStatus::getStatusApplication($application->status)['name']  }}</td>
                <td style="text-align: left">{{ $application->reason }}</td>
                <td style="text-align: left">2</td>
                <td style="text-align: left">{{ $application->position }}</td>
                <td style="text-align: left">
                    @if(count($application->applicationFiles) > 0)
                        @php $nameFile = '' @endphp
                        @foreach($application->applicationFiles as $file)
                            @php
                                $nameFile = $file->name.'-';
                                $nameFile =trim($nameFile ,'-');

                            @endphp
                        @endforeach
                    @endif
                    {{ $nameFile }}
                </td>
                <td style="text-align: left">
                    @php
                        $day = 0;
                    if (!is_null($application->dateTimeApplications))
                    {
                        foreach ($application->dateTimeApplications as $value)
                        {
                            $start = \Carbon\Carbon::createFromFormat('Y-m-d H:s:i' ,$value->information_day_2);
                            $end = \Carbon\Carbon::createFromFormat('Y-m-d  H:s:i' ,$value->information_day_4);
                            if ($value->information_day_1 == $value->information_day_3 && $start->diffInDays($end) == 0)
                            {
                                $day+=0.5;
                            }else
                            {
                                $day+= $start->diffInDays($end) + 1;
                            }

                        }
                    }
                    @endphp
                    {{ $day  }}
                </td>
                <td style="text-align: left">1</td>
            </tr>
        @endforeach
    @endif
    </tbody>
</table>
