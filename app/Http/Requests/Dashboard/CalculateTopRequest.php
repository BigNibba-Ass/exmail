<?php

namespace App\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;

class CalculateTopRequest extends FormRequest
{

    // TODO: remove
    public static array $points = ['Абакан', 'Анадырь', 'Архангельск', 'Астрахань', 'Барнаул', 'Белгород', 'Бийск', 'Биробиджан', 'Благовещенск', 'Братск', 'Брянск', 'Владивосток', 'Владикавказ', 'Владимир', 'Волгоград', 'Вологда', 'Воронеж', 'Горно-Алтайск', 'Грозный', 'Екатеринбург', 'Иваново', 'Ижевск', 'Йошкар-Ола', 'Иркутск', 'Казань', 'Калининград', 'Калуга', 'Кемерово', 'Киров', 'Комсомольск-на-Амуре', 'Кострома', 'Краснодар', 'Красноярск', 'Курган', 'Курск', 'Кызыл', 'Липецк', 'Магадан', 'Магнитогорск', 'Майкоп', 'Махачкала', 'МОСКВА', 'Мурманск', 'Набережные Челны', 'Назрань', 'Нальчик', 'Нарьян-Мар', 'Нижневартовск', 'Нижний Новгород', 'Великий Новгород', 'Новокузнецк', 'Новороссийск', 'Новосибирск', 'Новый Уренгой', 'Норильск', 'Ноябрьск', 'Омск', 'Орел', 'Оренбург', 'Пенза', 'Пермь', 'Петрозаводск', 'Петропавловск-Камчатский', 'Псков', 'Пятигорск', 'Ростов-на-Дону', 'Рязань', 'Салехард', 'Самара', 'Санкт-Петербург', 'Саранск', 'Саратов', 'Севастополь', 'Симферополь', 'Смоленск', 'Сочи', 'Ставрополь', 'Сургут', 'Сыктывкар', 'Тамбов', 'Тверь', 'Тольятти', 'Томск', 'Тула', 'Тюмень', 'Улан-Удэ', 'Ульяновск', 'Уфа', 'Хабаровск', 'Ханты-Мансийск', 'Чебоксары', 'Челябинск', 'Черкесск', 'Череповец', 'Чита', 'Элиста', 'Южно-Сахалинск', 'Якутск', 'Ярославль'];

    protected function prepareForValidation()
    {
        $this->merge([
            'nds_included' => boolval($this->nds_included),
        ]);
        if(is_string($this->selected_comparable_services)) {
            $this->merge([
                'selected_comparable_services' => json_decode($this->selected_comparable_services, true),
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'exmail_service_id' => ['required', 'int'],
            'where_from' => ['required', 'int'],
            'exmail_sale' => ['nullable', 'numeric'],
            'exmail_markup' => ['nullable', 'numeric'],
            'selected_comparable_services' => ['required', 'array'],
            'nds_included' => ['nullable', 'bool'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
